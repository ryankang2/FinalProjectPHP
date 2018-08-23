<?php

function getAddress($query){
    // $company_name = urlencode($company_name);
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://maps.googleapis.com/maps/api/place/textsearch/json?query=$query&key=AIzaSyAPvkDMnqCIkBzZRFX93Bccj5sF4YJa8F8",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "Cache-Control: no-cache",
        "Postman-Token: 16d7f649-a93a-45cc-a3cc-08135bc96c94"
      ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    
    print($response);
    // print_r($decoded_response);
    
}
getAddress('LearningFuze Irvine');

?>

