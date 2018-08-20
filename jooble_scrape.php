<?php
    function getplaintextintrofromhtml($url) {
        // include('simple_html_dom.php');
        
        $html = file_get_html($url);
        // point to the body, then get the innertext
        $data = $html->find('div[class=vacancy-desc_text_wrapper]', 0);
        // $data = $data->find('div[class=vacancy-desc_info]');
        return $data;
    }
    
    // echo getplaintextintrofromhtml('https://us.jooble.org/desc/-6670628439966473607?ckey=web+development&rgn=6974&pos=19&elckey=1511354777311894974&aq=17323661&age=114&relb=100&brelb=100&bscr=306,56226&scr=306,56226');
?>