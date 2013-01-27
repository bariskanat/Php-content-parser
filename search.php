<?php

class Search{
    
    use Message;
    /**
     *file name
     * @var string
     */
    private $file;
    
    
    /**
     * file or web page type 
     */
    
    private $type;
    /**
     *error from curl
     * @var string 
     */
    private $err;
    
    /**
     *
     * @var string 
     */
    private $content;
    
    /**
     *
     * @var string 
     */
    private $errmsg;
    
    /**
     *
     * @var type 
     */
    private $jobpage=["craigslist"];
    
    /**
     *file info
     * @var array
     */
    private $pageinfo=[];
    
    /**
     *
     * @var string 
     */
    private $header;
    
    /**
     *curl option
     * @var array 
     */
    private  $options = [
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_USERAGENT      => "author", // who am i
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
    ];
    
    /**
     *
     * @var array 
     */
    
    private $searchfor = [
            'softwaredeveloper'=>[                
                "JavaScript",
                "PHP",
                "HTML", 
                "CSS",
                "backbone",
                "jquery",
                "Wordpres",
                "CakePHP",
                "CodeIgniter",
                "Symfony",
                "Zend", 
                "Kohana",
                "LAMP"
                ],
            "city"=>[
                "new york",
                "boston",
                "miami",
                "san diego",
                "san fransisco",
                "paris",
                "berlin"
            ]
            ];
    
    /**
     * 
     * @param string $file
     */
    public function __construct($file)
    {
        $this->file=$file;       
        $this->getwebpage();
    }
   
    
    
    /**
     * 
     * @param string $URL
     * @return boolean
     */
    static function urlValid($URL)
    {
        $exists = true;
        $file_headers = @get_headers($URL);
        $InvalidHeaders = array('404', '403', '500');
        foreach($InvalidHeaders as $HeaderVal)
        {
                if(strstr($file_headers[0], $HeaderVal))
                {
                        $exists = false;
                        break;
                }
        }
        
       
        return $exists;
    }
    
    /**
     * 
     * @param string $file
     * @return \Search
     */
    
    public static function categorize($file)
    {
        
        if(!self::urlValid($file))return false;
        return (new self($file))->check();
        
        
    }
    
   
    
    /**
     * 
     * @return \Search
     */
    private function check()
    {
       
        foreach($this->searchfor as $key=>$value)
        {
            if(is_array($value))
            {
                foreach($value as $val)
                {
                    if(($result=$this->getinfo($val)))
                    {
                        $this->pageinfo[$key][$val]=$result;
                    }                   
                    
                }
            }
            $this->setcountnumber($this->pageinfo[$key]);
        }
        
        
       $this->type=$this->type();
       $this->info();
       return $this;
     
        
    }
    
    /**
     * 
     * @return mix
     */
    public function info()
    {
        if(!count($this->pageinfo)>0)return false;
        $key=$this->type;
        $this->pageinfo[$key]['type']=$key;
        $this->pageinfo[$key]['url']=$this->file;
        $this->parsehtml();
      
        return $this->pageinfo[$key];
    }
    
    /**
     * call web page parser
     */
    public function parsehtml()
    {
        foreach($this->jobpage as $key)
        {
            
            if(stripos($this->file,$key) && ($c=$this->{"get".strtolower($key)}()))
            {              
                    $this->pageinfo[$this->type]['details']=$c;     
                
            }
            
            
        }
        
    }
    
    /**
     * parse craiglist job page content
     * @return mix
     */
    public function getcraigslist()
    {
        if(($info=Craig::parse($this->file)))
        {
           
            $this->addMessage("craiglist","we successfully parse the page content");
           
            return $info;
        }
        
        $this->addMessage("craiglisterror", "we are not able to parse the page content");
        return false ;
    }
    
   
    
    /**
     * 
     * @return array
     */
    public function alldata()
    {
        return $this->pageinfo;
    }
    
    
    /**
     * 
     * @return string
     */
    public function type()
    {
        $num=0;
        $result="";
        foreach($this->alldata() as $key=>$val)
        {
            if(($newnum=$this->alldata()[$key]['count'])>$num)
            {
                $num=$newnum;
                $result=$key;
            }
             
        }
        
        return ($result)?$result:false;
    }
    
    
    /**
     * 
     * @param array $arr
     */
    private function setcountnumber(&$arr)
    {
      if(!is_array($arr))return false;
      $number=0;
      
        foreach($arr as $key => $val)
        {
          
            if(is_array($val))
            {              
                    $number+=count($val);
                    $arr['count']=$number;      
                
            }
        }
    }
    
    public function getcountnumber()
    {
        return ($number=$this->pageinfo[$this->type]['count'])?$number:false;
    }
    
      
    
    /**
     * 
     * @param atring $data
     * @return array
     */
    private function getinfo($data)
    {
        
        $pattern = "/^.*$data.*\$/im";  
     
        $this->content=  strip_tags($this->content);
        if(preg_match_all($pattern, $this->content, $matches))             
           return $this->newarr($matches[0]);           
        
    }
    
    /**
     * 
     * @param array $arr
     * @return array
     */
    
    private function newarr($arr)
    {
       
        return array_filter(array_map(function($value){
              
                return strip_tags(trim($value));
            },$arr));
    }
    
    /**
     * set curl
     */
    
    private function getwebpage()
    {
        

        $ch      = curl_init( $this->file );
        curl_setopt_array( $ch, $this->options );
        $this->content = curl_exec( $ch );
        $this->err     = curl_errno( $ch );
        $this->errmsg  = curl_error( $ch );
        $this->header  = curl_getinfo( $ch );

        curl_close( $ch );


    }
    
   
   
    
}
