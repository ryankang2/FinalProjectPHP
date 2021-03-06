<?php
    function getSalary($title, $city, $country){
        $spacePattern = '/[ ]/';
        $title = preg_replace($spacePattern, "-", $title);
        $city = preg_replace($spacePattern, "-", $city);
        
        if(!$city && !$country){
            $url = "https://www.indeed.com/salaries/$title"."-Salaries,-California";
        }
        else if($city && !$country){
            $url = "https://www.indeed.com/salaries/$title"."-Salaries,-"."$city"."-CA";
        }
        else{
            $url = "https://www.indeed.com/salaries/$title"."-Salaries";
        }
        $html = file_get_html($url);
        $salary = $html->find('span[class=cmp-salary-amount]', 0);
        $salary = $salary->innertext;
        $pattern = '/[$,]/';
        $salary = preg_replace($pattern, "", $salary); 
        $salary = (int)$salary;
        $salary = round($salary);
        $salary = (string)$salary;
        $length = strlen  ($salary);
        if($length < 3){
            $salary = ($salary * 40)*52;
        }

        return $salary;
    }

    // print(getSalary("ABAP Program Developer", "Irvine"));

?>
