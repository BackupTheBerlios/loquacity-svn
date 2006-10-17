<?php

class validateemail extends validator{
    /**
    * Stores the email to validate
    *
    * @access private
    * @var string
    */
    var $email;
    
    /**
    * Minimum length of email
    *
    * @access private
    * @var int
    */
    var $min;
    
    /**
    * Maximum length of email
    *
    * @access private
    * @var int
    */
    var $max;
    
    function validateemail($email=null){
        $this->email = $email;
        $this->min = 8;
        $this->max = 100;
        validator::validator();
    }
    function validate(){
        if(is_null($this->email) || strlen($this->email) == 0){
            $this->setError("No author name provided");
        }
        if(strlen($this->email) < $this->min){
            $this->setError("Minimum length of an author name is $this->min");
        }
        if(strlen($this->email) > $this->max){
            $this->setError("Maximum length of an author name is $this->max");
        }
        if(!preg_match('/^[a-zA-Z0-9_ a]+$/', $this->email)){
            $this->setError('Email address contains invalid characters. Only Letters, Numbers and _ are allowed');
        }
    }
}
