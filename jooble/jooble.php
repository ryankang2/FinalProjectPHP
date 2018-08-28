<?php
require_once("mysql_connect.php");
require_once("scrape/jooble_scrape.php");
require_once("scrape/salary_scrape.php");
require_once("api/clear_bit.php");
require_once("api/google_location.php");
include("scrape/simple_html_dom.php");
$url = "https://us.jooble.org/api/";
$key = "3fb7e81e-bb94-45d0-af8f-0df47f82bc31";

// $key = "205d18d5-59ca-474f-b770-b8e8fa04fca2";
//create request object
$ch = curl_init();
header('Content-Type: application/json');
curl_setopt($ch, CURLOPT_URL, $url."".$key);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{ "keywords": "web%20development", "location": "Irvine", "radius":"25", "page": 1}');
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
// receive server response ...
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$server_output = curl_exec ($ch);   
curl_close ($ch);
$server_output = json_decode($server_output);
$output = [
    "errors"=>[]
];
print_r($server_output);


for($i = 0; $i < count((array)$server_output->jobs); $i++){
    if($i === 3){
        break;
    }
    $title = $server_output->jobs[$i]->title;
    $link = $server_output->jobs[$i]->link;
    $postDate = getPostDate($server_output->jobs[$i]->updated);
    $type = (INT)getJobType($server_output->jobs[$i]);
    $companyName = $server_output->jobs[$i]->company;
    $titleCompany = "$title"."-"."$companyName";
    $location = $server_output->jobs[$i]->location;
    $city = preg_replace("/, CA/", "", $location);
    $revisedCompanyName = cleanCompanyName($companyName);
    $companySite = getCompanySite($revisedCompanyName);
    $revisedCompanyName = str_replace(" ", "", $revisedCompanyName);
    $revisedCompanyName = str_replace(",", "", $revisedCompanyName);
    //check if company site is invalid, if so, give it a .com concat
    $companySite = ($companySite==NULL) ? ($revisedCompanyName).".com":$companySite;


    //fill up companies table
    $checkDuplicateCompany = "SELECT `name` FROM `companies` WHERE `name`='$companyName'";
    $companyCheckQueryResult = mysqli_query($conn, $checkDuplicateCompany);
    if(mysqli_num_rows($companyCheckQueryResult) == 0){
        $clearBitObj = getClearBitObj($companySite);
        $linkedin_url = $clearBitObj["linkedin"];
        $ocr_url = $clearBitObj["ocr"];
        $crunchbase_url = $clearBitObj["crunch"];
        $logo = $clearBitObj["logo"];
        $query = "INSERT INTO `companies`(`name`, `company_website`, `linkedin_url`, `ocr_url`, `crunchbase_url`, `logo`) 
        VALUES ('$companyName', '$companySite', '$linkedin_url', '$ocr_url', '$crunchbase_url', '$logo')";
        $result = mysqli_query($conn, $query);
        if(!$result){
            $output["errors"][] = "failed to query company";
        }

        //insert into locations table
        $locationObj = getGoogleObj("$companyName "."$city");
        $address = $locationObj["results"][0]["formatted_address"];
        list($street, $city, $statezip) = explode(", ", $address);
        list($state, $zip) = explode(" ", $statezip);
        $lat = $locationObj["results"][0]["geometry"]["location"]["lat"];
        $long = $locationObj["results"][0]["geometry"]["location"]["lng"];
        $location_id = mysqli_insert_id($conn);
        $query = "INSERT INTO `locations`(`company_id`, `street`, `city`, `state`, `zip`, `lat`, `lng`, `full_address`)
        VALUES ($location_id, '$street','$city','$state', '$zip', '$lat', '$long', '$address')";
        $result = mysqli_query($conn, $query);
        if(!$result){
            $output["errors"][] = "failed to insert to locations table";
        }
    }
    //insert into salaries table
    $titleCity = "$title"."-"."$city";
    $checkDuplicateSalary = "SELECT `title_city` FROM `salaries` WHERE `title_city`='$titleCity'";
    $checkSalaryQueryResult = mysqli_query($conn, $checkDuplicateSalary);
    //if salary is not in salaries table, insert salary into it
    if(mysqli_num_rows($checkSalaryQueryResult) == 0){
        $citySalary = (INT)getSalary($title, $city, false);
        $stateSalary = (INT)getSalary($title, false, false);
        $nationalSalary = (INT)getSalary($title, false, true);
        $query = "INSERT INTO `salaries`(`city_salary`, `state_salary`, `national_salary`, `title_city`)
        VALUES ($citySalary, $stateSalary, $nationalSalary, '$titleCity')";
        $result = mysqli_query($conn, $query);
        if(!$result){
            $output["errors"][] = "failed to query salaries"; 
        }
        $salary_id = mysqli_insert_id($conn);
    }

    //skip appcast.io -> cant scape description
    if($server_output->jobs[$i]->source === "appcast.io"){
       continue;
    }

    //insert into jobs table
    $checkDuplicateJobs = "SELECT `title_comp` FROM `jobs` WHERE `title_comp`='$titleCompany'";
    $checkJobQueryResult = mysqli_query($conn, $checkDuplicateJobs);
    if(mysqli_num_rows($checkJobQueryResult) == 0){
        $description = addslashes(getJoobleDescription($link));
        //match up jobs.company_id to companies.ID in mysql
        $companyIDQuery = "SELECT c.ID FROM companies AS c WHERE c.name = '$companyName'";
        $result = mysqli_query($conn, $companyIDQuery);
        $row = mysqli_fetch_assoc($result);
        $company_id = $row["ID"];
        if($description == "No description available"){
            continue;
        }
        $query = "INSERT INTO `jobs`(`title`, `company_name`, `company_id`, `description`, `post_date`, `listing_url`, `type_id`, `title_comp`, `salary_id`) 
        VALUES ('$title', '$companyName', $company_id, '$description', '$postDate', '$link', $type, '$titleCompany', $salary_id)";
        //send the query to the database, store the result of the query into $result
        $result = mysqli_query($conn, $query);
        if(!$result){
           $output["errors"][] = "failed to query jobs"; 
        }
    }
    print_r($output);
}


//returns company name without extra keywords->easier to find domain of company
function cleanCompanyName($companyName){
    $companyName = strtolower($companyName);
    $pattern = "/corporation|usa|inc|connection|llc|america|services|corp|solutions|technology|company|research|\./";
    $companyName = preg_replace($pattern , '', $companyName);
    return $companyName;
}

//returns job type
function getJobType($job){
    $title = $job->title;
    $type = $job->type;
    if($type){
        $type = strtolower($type);
        switch($type){
            case "full-time":
                return 1;
            case "temporary":
                return 2;
            case "intern":
                return 3; 
            default: 
                return 1;
        }
    }
    else{
        $title = strtolower($title);
        if(preg_match("/full/", $title)){
            return 1;
        }
        else if(preg_match("/part/", $title) || preg_match("/contract/", $title)){
            return 2;
        }
        else if(preg_match("/intern/", $title)){
            return 3;
        }
        else{
            return 1;
        }
    }
}

//returns post date
function getPostDate($date){
    $microTime = strtotime($date);
    return date("m/d/Y", $microTime);
}





?>