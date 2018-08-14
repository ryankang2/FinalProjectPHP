<?php
require_once('mysql_connect.php');

$url = "http://api.adzuna.com/v1/api/jobs/us/search/1?app_id=3d4d7ffb&app_key=475a2f3207997bf788f4c809016f33f0&what=javascript&location0=US&location1=California&location2=Orange+County";   


//create request object
header('Content-Type: application/json');
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

$server_output = curl_exec ($ch);
curl_close ($ch);
$server_output = json_decode($server_output);
// echo count((array)$server_output -> results);
print_r($server_output);


for($i = 0;  $i < count((array)$server_output -> results); $i++){
    $title        = getJobTitle($server_output->results[$i]);
    $post_date    = getPostDate($server_output->results[$i]->created);
    $listing_url  = getListingURL($server_output -> results[$i]);

    $checkExistance = "SELECT `title` FROM `jobs` WHERE `title` = '$title'";
    mysqli_query($conn, $checkExistance);
    if(mysqli_affected_rows($conn) === 0){
        $query = "INSERT INTO `jobs`(`title`,`company_id`, `description_id`, `post_date`, `listing_url`) VALUES ('$title', 1, 1, '$post_date', '$listing_url')";
        $result = mysqli_query($conn, $query);
    }
}

function getPostDate($date){
    $microtime = strtotime($date);
    return date('Y-m-d H:i:s',$microtime);  
};

function getJobTitle($obj){
   return ($obj->title.", ".$obj->company->display_name);
};
function getListingURL($obj){
    return ($obj->redirect_url);
}



// //print response
// print_r($server_output);

?>