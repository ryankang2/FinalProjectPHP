<?php
    require_once('mysql_connect.php');
    require_once('clearBit.php');
    require_once('scraped_description.php');
    require_once('googlePlace.php');
    require_once('salary_scrape.php');


    $url = "https://api.adzuna.com:443/v1/api/jobs/us/search/1?app_id=79a0aa3c&app_key=c80d29a4d0a23378b7b0f66c95e5aaaf&results_per_page=1&what=back%20end%20developer&location0=US&location1=California&location2=Orange%20County";   

//create request object
    header('Content-Type: application/json');
    $ch = curl_init();                      
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

    $server_output = curl_exec ($ch);
    curl_close ($ch);
    $server_output = json_decode($server_output);
    print_r($server_output);

    $output = [
        'success' => false,
        'errors' =>[],
        'message'=> []
    ];
// loop through the results array and get the title, postDate and listingURL
    for($i = 0;  $i < count((array)$server_output -> results); $i++){
        // variables for JOBS table
        $currentResultIndex = $server_output->results[$i];
        $company_name = $currentResultIndex->company->display_name;
        $listing_title = getJobTitle($currentResultIndex);
        $title = $listing_title.','.$company_name;
        $title_name = $listing_title.'-'.$company_name;
        $post_date    = getPostDate($currentResultIndex->created);
        $listing_url  = getListingURL($currentResultIndex);
        $type_id = getJobType($currentResultIndex);
        $urlEncodedName= encodeName($company_name);
       
        
       
        $city = $currentResultIndex-> location->area[3];
        $address_query = $urlEncodedName." ".$city;
        
        
            
        $ocr_url = "https://www.ocregister.com/?s=".$company_name."&orderby=date&order=desc";
        $company_website = getDomain($urlEncodedName);    
        $clearbitObject = getClearbitObj($company_website);
        
// linkedIn: 
        if(isset($clearbitObject["linkedin"]["handle"])=== true){
            $linkedin_url= "www.linkedin.com/".$clearbitObject["linkedin"]["handle"];
        }
        else {
            $linkedin_url = NULL;
        }
        
//logo:
        if(isset( $clearbitObject["logo"])=== true){
            $logo = $clearbitObject["logo"];
        }
        else{
            $logo = NULL;
        };
        
// crunchbase:
        if(isset($clearbitObject["crunchbase"]["handle"])===true){
            $crunchbase = "www.crunchbase.com/".$clearbitObject["crunchbase"]["handle"];
        }
        else{
            $crunchbase = NULL;
        };

// run query to check companies table if current index exists in the database
        $checkCompanyExistance = "SELECT * FROM `companies` WHERE `name` = '$company_name'";
        $companyCheckQueryResult = mysqli_query($conn, $checkCompanyExistance);

        if(mysqli_num_rows($companyCheckQueryResult) === 0){
            $query2 = "INSERT INTO `companies` (`name`, `company_website`, `linkedIn_url`, `ocr_url`, `logo`,`crunchbase`) VALUES ('$company_name', '$company_website', '$linkedin_url','$ocr_url', '$logo', '$crunchbase')";
            $companyInsertQueryResult = mysqli_query($conn, $query2);
        // check if results from company insert query is empty/encountered an error    
            if(mysqli_affected_rows($conn)=== -1){
                $output['error'][]= "## Company insert query error";
            }
// add locations query
            $addressObject = getAddress($address_query);
            $fullAddress = $addressObject["fullAddress"];
            $lat = $addressObject["lat"];
            $long = $addressObject["long"];
            $street = $addressObject["street"];
            $city = $addressObject["city"];
            $state = $addressObject["state"];
            $zip = $addressObject["zip"]; 

            $company_id = mysqli_insert_id($conn);
            $location_query = "INSERT INTO `locations`(`company_id`, `street`,`city`,`state`,`lat`,`lng`,`full_address`) VALUES
            ($company_id, '$street','$city','$state','$lat','$long','$fullAddress')";
            $locationInsertQueryResult = mysqli_query($conn, $location_query);
            if(mysqli_affected_rows($conn)=== -1){
                $output['error'][]= "## Locations insert query error";
            }
        }
// Salary:

        //fill up salaries table
        $titleCity = "$listing_title"."-"."$city";
        $checkDuplicateSalary = "SELECT `title_city` FROM `salaries` WHERE `title_city`='$titleCity'";
        $salaryCheckQueryResult = mysqli_query($conn, $checkDuplicateSalary);
        
        if(mysqli_num_rows($salaryCheckQueryResult) === 0){
        //if salary is not in salaries table, insert salary into it
    
            $citySalary = (INT)getSalary($title, $city, false);
            $stateSalary = (INT)getSalary($title, false, false);
            $nationalSalary = (INT)getSalary($title, false, true);


            $salaryInsertQuery = "INSERT INTO `salaries`(`city_salary`, `state_salary`, `title_city`, `national_salary`)
            VALUES ($citySalary, $stateSalary, '$titleCity', $nationalSalary)";
            $salaryInsertQueryResult = mysqli_query($conn, $salaryInsertQuery);
            if(mysqli_affected_rows($conn)=== -1){
                $output["errors"][] = "failed to query salaries"; 
            }
            $salary_id = mysqli_insert_id($conn);
        }
 
// write query to select titles that are repeated
        $checkJobExistance = "SELECT * FROM `jobs` WHERE `title_name` = '$title_name'";
        $jobCheckQueryResult = mysqli_query($conn, $checkJobExistance);
            
        if(mysqli_num_rows($jobCheckQueryResult)=== 0 && $description !== NULL){ 

            $companyIDQuery = "SELECT c.ID FROM companies AS c WHERE c.name = '$company_name'";
            $companySelectQueryResult = mysqli_query($conn, $companyIDQuery);
            $row = mysqli_fetch_assoc($companySelectQueryResult);
            $company_id = $row["ID"];
            $description = scrapeDescription($listing_url);
            
            $jobsInsertQuery = "INSERT INTO `jobs`
            (`title`, `company_id`, `description`, `post_date`, `listing_url`, `type_id`, `company_name`, `title_name`, `salary_id`) 
            VALUES ('$listing_title', $company_id, '$description', '$post_date', '$listing_url', $type_id, '$company_name', '$title_name', $salary_id)";
            $jobsInsertQueryResult = mysqli_query($conn, $jobsInsertQuery);  
                if(mysqli_affected_rows($conn)=== -1){
                    if($description === null){
                        $output['error'][]= "## Jobs description is unavailable, did not insert job";
                    }
                    else {
                        $output['error'][]= "## Jobs insert query error";
                    }   
                } 
            }
    }
    print_r($output);
//------------------------------------------------------------------------------------------------------------------//
    

    function encodeName($company_name){
        $company_name = strtolower($company_name);
        $company_name = preg_replace('/(corporation|usa|inc|connection|llc|america|services|corp|solutions|research|company|orange county|\.|\,)/','', $company_name);
        return urlencode($company_name);
    };


    function getPostDate($date){
        $microtime = strtotime($date);
        return date('m/d/Y',$microtime);  
    };

    function getJobTitle($obj){
    $temp= ($obj->title);
    $temp = strtolower($temp);
    $temp = ucfirst($temp);
    return strip_tags($temp);
    };
    function getListingURL($obj){
        return ($obj->redirect_url);
    }

    function getJobType($obj){
        $title = strtolower($obj-> title);
        
        $type = isset($obj-> contract_time) ? $obj-> contract_time : NULL;
        if($type){
            switch($type){
                case 'full_time':
                    return 1;
                case 'part_time':
                    return 2;
                case 'contract':
                    return 2;
                case 'internship':
                    return 3;
                case 'intern':
                    return 3;
            }
        }else {
            if(preg_match('/full/', $title)){
                return 1;
            }
            else if(preg_match('/part/', $title) || preg_match('/contract/', $title)){
                return 2;
            }
            else if(preg_match('/intern/', $title)){
                return 3;
            }
            else{
                return 1;
            }
        }
    }

?>