<?php
include_once("simple_html_dom.php");
    function getSalary($title, $city){
        $spacePattern = '/[ ]/';
        $title = preg_replace($spacePattern, "-", $title);
        $city = preg_replace($spacePattern, "-", $city);
        $url = ($city === "") ? "https://www.indeed.com/salaries/$title"."-Salaries,-California" : 
                                "https://www.indeed.com/salaries/$title"."-Salaries,-"."$city"."-CA";
        
        $html = file_get_html($url);
        $salary = $html->find('span[class=cmp-salary-amount]', 0);
        $salary = $salary->innertext;
        $pattern = '/[$,]/';
        $salary = preg_replace($pattern, "", $salary); 
        return $salary;
    }

    // print(getSalary("ABAP Program Developer", "Irvine"));

?>