<?php
    function getSalary($title, $city, $country){
        $spacePattern = '/[ ]/';
        $title = preg_replace($spacePattern, "-", $title);
        $city = preg_replace($spacePattern, "-", $city);
        // $url = ($city === false) ? "https://www.indeed.com/salaries/$title"."-Salaries,-California" : 
        //                         "https://www.indeed.com/salaries/$title"."-Salaries,-"."$city"."-CA";
        // if($country)
        if(!$city && !$country){
            $url = "https://www.indeed.com/salaries/$title"."-Salaries,-California";
        }
        else if($city && !$country){
            $url = "https://www.indeed.com/salaries/$title"."-Salaries,-"."$city"."-CA";
        }
        else{
            $url = "https://www.indeed.com/salaries/$title"."-Salaries";
        }
        // print($url);
        $html = file_get_html($url);
        $salary = $html->find('span[class=cmp-salary-amount]', 0);
        $salary = $salary->innertext;
        $pattern = '/[$,]/';
        $salary = preg_replace($pattern, "", $salary); 
        $salary = round((INT)$salary);
        $num_length = strlen((string)$salary);
        if($num_length < 3){
            $salary = $salary*40*52;
        }
        return $salary;
    }

?>