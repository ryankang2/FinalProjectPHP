<?php

function getDomain($company_name){
    $arr = explode(' ', trim($company_name));
    $revisedCompanyName = $arr[0];
    
    print_r("<br>");
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://company.clearbit.com/v1/domains/find?name=:".$revisedCompanyName."&key=sk_b504931a3d3c61d5979227e102831b4b",
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
    
    $decoded_response = json_decode($response, true);
   

    return $decoded_response["domain"];



    // print_r('after decode:', $assoc);

    // print($assoc[0])
   
    // if ($err) {
    //     echo "cURL Error #:" . $err;
    // } else {
    //     echo $response;
    // }
        




}

?>

