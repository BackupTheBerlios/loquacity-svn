<?php

class validatepassword extends validator{
    /**
    * Stores the password to validate
    *
    * @access private
    * @var string
    */
    var $pword;
    
    /**
    * Minimum length of Password
    *
    * @access private
    * @var int
    */
    var $min;
    
    /**
    * Maximum length of Password
    *
    * @access private
    * @var int
    */
    var $max;
    
    function validatepassword($pword1=null, $pword2=null){
        $this->pword1 = $pword1;
        $this->pword2 = $pword2;
        $this->min = 8;
        $this->max = 40;
        validator::validator();
    }
    function validate(){
        if(is_null($this->pword1) || strlen($this->pword1) == 0){
            $this->setError("No Password provided");
        }
        if(is_null($this->pword2) || strlen($this->pword2) == 0){
            $this->setError("No validation password provided");
        }
        if(strlen($this->pword1) < $this->min){
            $this->setError("Minimum length of a password is $this->min");
        }
        if(strlen($this->pword1) > $this->max){
            $this->setError("Maximum length of a password is $this->max");
        }
        if($this->pword1 !== $this->pword2){
            $this->setError('Passwords do not match');
        }
    }
}
