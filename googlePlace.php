<?php

function getAddress($address_query){
    $output = [];
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
        
        list($street, $city, $statezip) = explode(", ",$formatted_address);
        list($state, $zip) = explode(" ", $statezip);
        
        $lat = $response["results"][0]["geometry"]["location"]["lat"];
        $long = $response["results"][0]["geometry"]["location"]["lng"];

        $output["fullAddress"] = $formatted_address;
        $output["lat"]= $lat;
        $output["long"]= $long;
        $output["street"]= $street;
        $output["city"]= $city;
        $output["state"]= $state;
        $output["zip"]= $zip;
        
        return $output;
    //    print_r ($output);
    }
    else{
        $output["fullAddress"] = NULL;
        $output["lat"]= NULL;
        $output["long"]= NULL;
        $output["street"]= NULL;
        $output["city"]= NULL;
        $output["state"]= NULL;
        $output["zip"]= NULL;
        return $output;
       
    } 
}
getAddress('LearningFuze irvine');

?>

