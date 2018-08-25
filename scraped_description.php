<?php
include('simple_html_dom.php');

function scrapeDescription($url){
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    
    $a = curl_exec($ch);
    
    $redirectedUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch); 
    $redirectHtml = file_get_html($redirectedUrl);
    $data =$redirectHtml->find('a', 0);
    $finalUrl = $data->href;  
    // print("redirectUrl: $redirectedUrl");
    // print("<br>");
   
    $output= '';
try {
    //ziprecruiter (working)
    if(preg_match('/ziprecruiter/', $finalUrl) === 1){
        $html = file_get_html($finalUrl);
        if(!$html){
            $output= "Listing is no longer available";
        }
        else{
            $scrapedData = $html->find('div[class=jobDescriptionSection]', 0);
            $output= $scrapedData;
        }  
    } 
    // dice (working)
    else if(preg_match('/dice/', $finalUrl) === 1 ){
        // print("finalUrl: $finalUrl");
        $html = file_get_html($finalUrl); // will return false if posting is no longer available
        if(!$html){
            $output = "Listing is no longer available";
        }
        else {
            $scrapedData = $html->find('div[id=jobdescSec]', 0);
            $output = $scrapedData;
        }
        // $scrapedData = (($html->find('div[id=jobdescSec]', 0))==null) ? "no job descrp from dice": $html->find('div[id=jobdescSec]', 0); // not currently using, not functional 
    } 
    // start wire !!redirects through appcast
    else if(preg_match('/appcast/', $finalUrl)=== 1){
       $output = NULL;
        
       
    }
    else {
        $output= "NO DESCRIPTION";
    }
} catch (Exception $errorr){
    return 'ERROR PULLING THE DESCRIPTION';
}

    return $output;
}

//dice:
// scrapeDescription('https://www.adzuna.com/land/ad/920627606?se=ytLHU2-5SiqWhN1Z066WHA&utm_medium=api&utm_source=79a0aa3c&v=53327A65470AC95096522786B59D259327E8905F');    



// ziprecruiter :
// scrapeDescription('https://www.adzuna.com/land/ad/914899043?se=TSpJH9FBS9u17oAdeihKGQ&utm_medium=api&utm_source=79a0aa3c&v=D3644A49A8CB8337A4B92E22B92C52E1A5AB910D');


// scrapeDescription('https://www.adzuna.com/land/ad/646291256?v=F63D34EFD943F196489369E186F6FCB5062C14C8&se=F-_SCsdiQouXvY3DHwaATQ');


// ?>

