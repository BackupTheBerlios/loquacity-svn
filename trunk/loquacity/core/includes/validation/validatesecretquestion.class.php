<?php

class validatesecretquestion extends validator{
    /**
    * Stores the secret question to validate
    *
    * @access private
    * @var string
    */
    var $secret;
    
    /**
    * Minimum length of secret question name
    *
    * @access private
    * @var int
    */
    var $min;
    
    /**
    * Maximum length of secret question
    *
    * @access private
    * @var int
    */
    var $max;
    
    function validatesecretquestion($secret=null){
        $this->secret = $secret;
        $this->min = 6;
        $this->max = 60;
        validator::validator();
    }
    function validate(){
        if(is_null($this->secret) || strlen($this->secret) == 0){
            $this->setError("No author name provided");
        }
        if(strlen($this->secret) < $this->min){
            $this->setError("Minimum length of an author name is $this->min");
        }
        if(strlen($this->secret) > $this->max){
            $this->setError("Maximum length of an author name is $this->max");
        }
        if(!preg_match('/^[a-zA-Z0-9_ a]+$/', $this->secret)){
            $this->setError('Secret question contains invalid characters. Only Letters, Numbers and _ are allowed');
        }
    }
}
