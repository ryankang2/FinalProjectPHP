<?php
    function getCompanySite($name){
        $curl = curl_init();
        $name = urlencode($name);
        
        print("name is: $name");

        //CLEARBIT name to domain API
        // curl_setopt_array($curl, array(
        // CURLOPT_URL => "https://company.clearbit.com/v1/domains/find?name=$name",
        // CURLOPT_RETURNTRANSFER => true,
        // CURLOPT_ENCODING => "",
        // CURLOPT_MAXREDIRS => 10,
        // CURLOPT_TIMEOUT => 30,
        // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        // CURLOPT_CUSTOMREQUEST => "GET",
        // CURLOPT_HTTPHEADER => array(
        //     "Authorization: Basic c2tfYjUwNDkzMWEzZDNjNjFkNTk3OTIyN2UxMDI4MzFiNGI6",
        //     "Cache-Control: no-cache",
        //     "Postman-Token: c6c737af-6223-4c2d-be11-d2294792c60a"
        // ),
        // ));

        //CLEARBIT autocomplete api
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://autocomplete.clearbit.com/v1/companies/suggest?query=$name",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
              "Cache-Control: no-cache",
              "Postman-Token: e8fc93e3-e95d-4944-ad7e-943eed46a1c7"
            ),
          ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $response_decoded = json_decode($response, true);
        // print_r($response_decoded);
        if(count($response_decoded) > 0){
            return $response_decoded[0]["domain"];
        }
        else{
            return null;
        }
    }

    function getClearBitObj($domain){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://company.clearbit.com/v2/companies/find?domain=$domain",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Basic c2tfYjUwNDkzMWEzZDNjNjFkNTk3OTIyN2UxMDI4MzFiNGI6",
            "Cache-Control: no-cache",
            "Postman-Token: 77bed0e9-10ec-43ac-aa26-9296dab9fa9e"
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $response_decoded = json_decode($response, true);
        print_r($response_decoded);
        return $response_decoded;
    }


?>