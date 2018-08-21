<?php

function getDomain($company_name){
    echo $company_name;
    print_r("<br>");
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://company.clearbit.com/v1/domains/find?name=$company_name",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "Authorization: Basic c2tfYjUwNDkzMWEzZDNjNjFkNTk3OTIyN2UxMDI4MzFiNGI6",
        "Cache-Control: no-cache",
        "Postman-Token: 9dea2d17-e13e-4d5a-aef8-ae5d20386a31"
      ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    curl_close($curl);
    
    
    $decoded_response =  json_decode($response);
    // print_r($decoded_response);
    // return $decoded_response->domain;

    if(isset($decoded_response ->domain)){
        return $decoded_response->domain;
    } else {
        print_r($decoded_response);
        print("<br>");
    }
    
    // if ($err) {
    //   echo "cURL Error #:" . $err;
    // } else {
    //     $server_output = json_decode($response);
    //     if($server_output ->domain){
    //         echo $server_output->domain;
    //     }
        
    // }



}

?>

