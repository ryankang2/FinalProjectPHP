<?php

function getDomain($company_name){
    $output;

    $curl = curl_init();
    curl_setopt_array($curl, array(
      // CURLOPT_URL => "https://company.clearbit.com/v1/domains/find?name=:".$revisedCompanyName."&key=sk_b504931a3d3c61d5979227e102831b4b",
      CURLOPT_URL => "https://autocomplete.clearbit.com/v1/companies/suggest?query=".$company_name,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "Cache-Control: no-cache",
        "Postman-Token: bf5f42fe-cd0a-4b90-9f63-ec1f45dfefb4"
      ),
    ));

    $response = curl_exec($curl); // execute the curl
    $err = curl_error($curl);
    curl_close($curl);  // close the curl
    
    // print_r('@response from getDomain call: '.$response); //response from getDomain call: [{"name":"Spectrum","domain":"spectrum.net","logo":"https://logo.clearbit.com/spectrum.net"}

    $decoded_response = json_decode($response, true);
    if(isset($decoded_response[0]["domain"])=== true){
      return $decoded_response[0]["domain"];
    } else {
      return NULL;   
    }    
    // print_r('@@company_domain:  '.$company_domain);
    // return$company_domain;   
}

function getClearbitObj($company_website){
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://company.clearbit.com/v2/companies/find?domain=".$company_website,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
      "Authorization: Basic c2tfZDViMWJhM2U5NmZjNjhlYTM5YTliNGY4NTkwZDAzN2U6",
      "Cache-Control: no-cache",
      "Postman-Token: ce322dca-91af-424f-b4a5-0afe17756ff2"
    ),
  ));
  
  $response = curl_exec($curl);
 
  // print('@@ Clearbit object response: '.$response);
  $err = curl_error($curl);
  
  curl_close($curl);
  return json_decode($response, true);
 
}

?>

