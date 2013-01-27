<?php

trait Message{
    
    public $messages=[];
    
    
    
    public function getMessage($key)
    {
        return (isset($this->messages[$key]))?$this->messages[$key]:false;
    } 
    
    private function addMessage($key,$val)
    {
        $this->messages[$key]=$val;
    }
    
    
    public function hasMessage($key)
    {
        return (array_key_exists($key, $this->messages));
    }
    
    public function allMessage()
    {
        return (count($this->messages))? $this->messages:false;
    }
    
    
    public function emptyAllMessage()
    {
        $this->messages=[];
    }
    
    public function Messagejson()
    {
        return (count($this->messages))?json_encode($this->messages):false;
    }
   
}


