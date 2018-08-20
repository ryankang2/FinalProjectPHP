<?php
    function getplaintextintrofromhtml($url) {
        // include('simple_html_dom.php');
        
        $html = file_get_html($url);
        // point to the body, then get the innertext
        $data = $html->find('div[class=vacancy-desc_text_wrapper]', 0);
        // $data = $data->find('div[class=vacancy-desc_info]');
        return $data;
    }
    
?>