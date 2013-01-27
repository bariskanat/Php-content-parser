
        <?php
       require_once 'message.php';
       require_once 'search.php';
       require_once 'simple_html_dom.php';
       require_once "craig.php";
       
       
      
        $file="url.com";
        
       
       
       
        $url=Search::categorize($file);
     
        print_r($url->info());
        var_dump($url->getcountnumber());
        var_dump($url->allMessage());
        var_dump($url->type());
     