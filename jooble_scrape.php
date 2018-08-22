<?php
// include("simple_html_dom.php");
    function getplaintextintrofromhtml($url) {
        // include('simple_html_dom.php');
        
        $html = file_get_html($url);
        // point to the body, then get the innertext
        $data = $html->find('div[class=vacancy-desc_text_wrapper]', 0);
        // $data = $data->find('div[class=vacancy-desc_info]');
        return $data;
    }

    // function scrape($url){
    //     $html = file_get_html($url);
    //     $data = $html->find('div[class=jobDescriptionSection]', 0);
    //     print($data);
    // }
    // // scrape("https://www.ziprecruiter.com/jobs/my-office-apps-inc-0fe3aeb3/progress-software-developer-b6505547?mid=8689&source=cpc-adzuna-priority");

    // function diceScrape($url){
    //     $html = file_get_html($url);
    //     $data = $html->find('div[id=jobdescSec]', 0);
    //     print($data);
    // }

    // diceScrape("https://www.dice.com/jobs/detail/9581d4bcfbffa760ed9dcede382a75c8?src=32&CMPID=AG_ADZ_PD_JS_US_OG__&utm_campaign=Advocacy_Ongoing&utm_medium=Aggregators&utm_source=Adzuna&rx_campaign=adzuna26&rx_group=103204&rx_job=10477632%2F9581d4bcfbffa760ed9dcede382a75c8&rx_medium=cpc&rx_source=Adzuna");
?>