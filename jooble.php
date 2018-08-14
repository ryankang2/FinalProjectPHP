<?php
require_once("mysql_connect.php");

$url = "https://us.jooble.org/api/";
$key = "3fb7e81e-bb94-45d0-af8f-0df47f82bc31";

//create request object
$ch = curl_init();
header('Content-Type: application/json');
curl_setopt($ch, CURLOPT_URL, $url."".$key);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{ "keywords": "software", "location": "Irvine", "radius":"25", "page": 1}');
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

// receive server response ...
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec ($ch);   
curl_close ($ch);
$server_output = json_decode($server_output);

for($i = 0; $i < count((array)$server_output->jobs); $i++){
    $title = getJobTitle($server_output->jobs[$i]);
    $postDate = getPostDate($server_output->jobs[$i]->updated);
    $link = getListingURL($server_output->jobs[$i]);
    $checkQuery = "SELECT `title` FROM `jobs` WHERE `title`='$title'";
    mysqli_query($conn, $checkQuery);
    //no duplicate jobs inserted with jobtitle, company
    if(mysqli_affected_rows($conn) == 0){
        $query = "INSERT INTO `jobs`(`title`, `company_id`, `description_id`, `post_date`, `listing_url`) 
        VALUES ('$title', 1, 2, '$postDate', '$link')";
        //send the query to the database, store the result of the query into $result
        $result = mysqli_query($conn, $query);
    }
}

//returns post date
function getPostDate($date){
    $microTime = strtotime($date);
    return date('Y-m-d H:i:s', $microTime);
}

//return job title (job title, company name)
function getJobTitle($job){
    $company_name = $job->company;
    return $job->title.", ".$company_name;
}

function getListingURL($job){
    return $job->link;
}

//print response
print_r($server_output);




?>