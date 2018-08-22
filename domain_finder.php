<?php
    function getCompanySite($name){
        $curl = curl_init();
        // $arr = explode(' ', trim($name));
        // $name = $arr[0];
        $name = urlencode($name);

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://company.clearbit.com/v1/domains/find?name=$name",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Basic c2tfYjUwNDkzMWEzZDNjNjFkNTk3OTIyN2UxMDI4MzFiNGI6",
            "Cache-Control: no-cache",
            "Postman-Token: c6c737af-6223-4c2d-be11-d2294792c60a"
        ),
        ));
    
        $response = curl_exec($curl);
        $err = curl_error($curl);
    
        curl_close($curl);
        $response_decoded = json_decode($response, true);
        print_r($response_decoded);

        return $response_decoded["domain"];
    }


?>