<?php

function getAddress($address_query){
    // $company_name = urlencode($company_name);
    $curl = curl_init();
    $address_query = urlencode($address_query);
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://maps.googleapis.com/maps/api/place/textsearch/json?query=$address_query&key=AIzaSyAPvkDMnqCIkBzZRFX93Bccj5sF4YJa8F8",
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
    // print('@@@@ json response'.$response);
    $err = curl_error($curl);
    curl_close($curl);
   
    $response = json_decode($response, true);
    // print_r($response);
    
    if(isset($response["results"][0])=== true){
       $formatted_address= $response["results"][0]["formatted_address"];
       return $formatted_address;
    }
    else{
        return NULL;
    }
    


  
}
getAddress('LearningFuze Irvine');

?>

