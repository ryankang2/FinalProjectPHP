<?php
    require_once('mysql_connect.php');
    require_once('clearBit.php');


    $url = "https://api.adzuna.com:443/v1/api/jobs/us/search/1?app_id=79a0aa3c&app_key=c80d29a4d0a23378b7b0f66c95e5aaaf&results_per_page=20&what=software%20developer&location0=US&location1=California&location2=Orange%20County";   


    //create request object
    header('Content-Type: application/json'); // specify data type
    $ch = curl_init();                      // initiate request
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

    $server_output = curl_exec ($ch);
    curl_close ($ch);
    $server_output = json_decode($server_output);
    // echo count((array)$server_output -> results);
    print_r($server_output);

    // loop through the results array and get the title, postDate and listingURL
    for($i = 0;  $i < count((array)$server_output -> results); $i++){
        // variables for JOBS table
            $currentResultIndex = $server_output->results[$i];
            $title = getJobTitle($currentResultIndex);
            $post_date    = getPostDate($currentResultIndex->created);
            $listing_url  = getListingURL($currentResultIndex);
            $type_id = getJobType($currentResultIndex);
            $company_name = $currentResultIndex->company->display_name;
            // echo $type_id;
        // variables for COMPANIES table
            $revisedCompanyName = str_replace(' ', '-', $company_name); // removes spaces 

            $linkedIn_url = 'https://www.linkedin.com/company/'.$revisedCompanyName;
            $ocr_url = "https://www.ocregister.com/?s=".$company_name."&orderby=date&order=desc";
            $company_website = getDomain($company_name);
            
        // run query to check companies table if current index exists in the database
            $checkCompanyExistance = "SELECT * FROM `companies` WHERE `name` = '$company_name'";
            $companyQueryResult = mysqli_query($conn, $checkCompanyExistance);
        // if 
        if(mysqli_num_rows($companyQueryResult) === 0){
            $query2 = "INSERT INTO `companies` (`name`, `location_id`, `company_website`, `linkedIn_url`, `ocr_url`)VALUES ('$company_name', 1, '$company_website', '$linkedIn_url','$ocr_url')";
            $result2 = mysqli_query($conn, $query2);

        }



        // write query to select titles that are repeated
        $checkJobExistance = "SELECT `title` FROM `jobs` WHERE `title` = '$title'";
        $jobQueryResult = mysqli_query($conn, $checkJobExistance);
        if(mysqli_num_rows($jobQueryResult)=== 0){ 
            $companyIDQuery = "SELECT c.ID FROM companies AS c WHERE c.name = '$company_name'";
            $result = mysqli_query($conn, $companyIDQuery);
            if(!$result){
                $output["errors"][] = "failed to companyIDQuery";
            }
            $row = mysqli_fetch_assoc($result);
            $company_id = $row["ID"];

            $query = "INSERT INTO `jobs`(`title`,`company_id`, `description`, `post_date`, `listing_url`, `type_id`, `company_name`) VALUES ('$title', $company_id, 1, '$post_date', '$listing_url', '$type_id', '$company_name')";
            $result = mysqli_query($conn, $query);  
        }
    }

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