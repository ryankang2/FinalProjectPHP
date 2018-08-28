<?php

    function getGoogleObj($query){
        $curl = curl_init();
        $query = urlencode($query);
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://maps.googleapis.com/maps/api/place/textsearch/json?query=$query&key=AIzaSyDD-MNI6C_FMEbCVx1xJx5eD2LSffA_FZE",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Basic Og==",
            "Cache-Control: no-cache",
            "Postman-Token: 00cae4e2-2645-4b9a-b4d7-1d49f8c415af"
        ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);


    }
?>