<?php

function getDomain($company_name){
    $output;
    // $arr = explode(' ', trim($company_name));
    // $revisedCompanyName = $arr[0];
    $company_name = strtolower($company_name);
    $company_name = preg_replace('/(corporation|usa|inc|connection|llc|america|services|corp|solutions|\.|\,)/','', $company_name);
   $company_name = urlencode($company_name);

    print('@@@url encode company name: '.$company_name);

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
        "Authorization: Basic c2tfYjUwNDkzMWEzZDNjNjFkNTk3OTIyN2UxMDI4MzFiNGI6",
        "Cache-Control: no-cache",
        "Postman-Token: 9dea2d17-e13e-4d5a-aef8-ae5d20386a31"
      ),
    ));

    $response = curl_exec($curl); // execute the curl
    $err = curl_error($curl);
    curl_close($curl);  // close the curl
    
    // print_r('@response from getDomain call: '.$response); //response from getDomain call: [{"name":"Spectrum","domain":"spectrum.net","logo":"https://logo.clearbit.com/spectrum.net"}

    $decoded_response = json_decode($response, true);
    $company_website = $decoded_response[0]["domain"];

    if(isset($company_website)){
      $output = $company_website;
      return $output;
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
      "Authorization: Basic c2tfYjUwNDkzMWEzZDNjNjFkNTk3OTIyN2UxMDI4MzFiNGI6",
      "Cache-Control: no-cache",
      "Postman-Token: 21083407-2a76-4668-adb5-df062388f3f6"
    ),
  ));
  
  $response = curl_exec($curl);
 
  // print('@@ Clearbit object response: '.$response);
  $err = curl_error($curl);
  
  curl_close($curl);
  return json_decode($response, true);
 
}

?>

