<?php
require_once("mysql_connect.php");
require_once("jooble_scrape.php");
require_once("domain_finder.php");
include("simple_html_dom.php");
$url = "https://us.jooble.org/api/";
// $key = "3fb7e81e-bb94-45d0-af8f-0df47f82bc31";

$key = "205d18d5-59ca-474f-b770-b8e8fa04fca2";
//create request object
$ch = curl_init();
header('Content-Type: application/json');
curl_setopt($ch, CURLOPT_URL, $url."".$key);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{ "keywords": "software", "location": "Irvine", "radius":"25", "page": 2}');
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
    $title = getJobTitle($server_output->jobs[$i]);
    $postDate = getPostDate($server_output->jobs[$i]->updated);
    $link = getListingURL($server_output->jobs[$i]);
    $type = (INT)getJobType($server_output->jobs[$i]);
    $description = addslashes(getplaintextintrofromhtml($link));
    $company_name = $server_output->jobs[$i]->company;
    $title_company = "$title"."-"."$company_name";
    $company_site = getCompanySite($company_name);
    $company_site = ($company_site==NULL) ? urlencode($company_name).".com":$company_site;
    //fill up companies table
    $checkDuplicateCompany = "SELECT `name` FROM `companies` WHERE `name`='$company_name'";
    mysqli_query($conn, $checkDuplicateCompany);
    if(mysqli_affected_rows($conn) == 0){
        $revisedCompanyName = str_replace(" ", "-", $company_name);
        $linkedin_url = "https://www.linkedin.com/company/".$revisedCompanyName."/";
        $ocr_url = "https://www.ocregister.com/?s=$company_name&orderby=date&order=desc";
        $query = "INSERT INTO `companies`(`name`, `location_id`, `company_website`, `linkedin_url`, `ocr_url`) 
        VALUES ('$company_name', 1, '$company_site', '$linkedin_url', '$ocr_url')";
        $result = mysqli_query($conn, $query);
        if(!$result){
            $output["errors"][] = "failed to query company";
        }
    }
    //fill up jobs table
    $checkDuplicateJobs = "SELECT `title_comp` FROM `jobs` WHERE `title_comp`='$title_company'";
    mysqli_query($conn, $checkDuplicateJobs);
    if(mysqli_affected_rows($conn) == 0){
        $companyIDQuery = "SELECT c.ID FROM companies AS c WHERE c.name = '$company_name'";
        $result = mysqli_query($conn, $companyIDQuery);
        $row = mysqli_fetch_assoc($result);
        $company_id = $row["ID"];
        
        $query = "INSERT INTO `jobs`(`title`, `company_name`, `company_id`, `description`, `post_date`, `listing_url`, `type_id`, `title_comp`) 
        VALUES ('$title', '$company_name', $company_id, '$description', '$postDate', '$link', $type, '$title_company')";
        echo $query;
        //send the query to the database, store the result of the query into $result
        $result = mysqli_query($conn, $query);
        if(!$result){
           $output["errors"][] = "failed to query jobs"; 
        }
    }

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
//return job title (job title, company name)
function getJobTitle($job){
    return $job->title;
}

function getListingURL($job){
    return $job->link;
}
//print response
// print_r($server_output);
print_r($output);
?>