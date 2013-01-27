<?php

class Craig{
    
    private static $file;
    private static $html;

    public static function parse($file)
    {
      
        self::$file=$file;
        self::$html=new simple_html_dom();
        self::$html->load_file($file);
        $title=self::getjobtitle();       
        $description=  trim(strip_tags(self::getdescription()));
         
        if($title && $description)
        {
            return [
               "title"         =>$title,
                "description"  =>$description
            ];
        }
        
        return false;
       
    }
    
    private static function getjobtitle()
    {
        $title=self::$html->find("h2[class=postingtitle]",0);
        return ($title) ?$title->innertext:false;
    }
    
    
    private static function getdescription()
    {
     
        $title=self::$html->find("section[id=postingbody]",0);
        return ($title) ?$title->innertext:false;
    }
}