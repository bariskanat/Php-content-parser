
        <?php
       require_once 'message.php';
       require_once 'search.php';
       require_once 'simple_html_dom.php';
       require_once "craig.php";
       
       
      
        $file="http://newyork.craigslist.org/mnh/egr/3554996277.html";
        
       
       
       
        $url=Search::categorize($file);
     
        print_r($url->info());
        var_dump($url->getcountnumber());
        var_dump($url->allMessage());
        var_dump($url->type());
     