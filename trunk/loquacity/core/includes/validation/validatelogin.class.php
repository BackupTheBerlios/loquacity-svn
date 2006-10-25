<?php

class validatelogin extends validator{
    /**
    * Stores the login name to validate
    *
    * @access private
    * @var string
    */
    var $login;
    
    /**
    * Minimum length of login names
    *
    * @access private
    * @var int
    */
    var $min;
    
    /**
    * Maximum length of login name
    *
    * @access private
    * @var int
    */
    var $max;
    
    function validatelogin($login=null){
        $this->login = $login;
        $this->min = 6;
        $this->max = 20;
        validator::validator();
    }
    function validate(){
        if(is_null($this->login) || strlen($this->login) == 0){
            $this->setError("No login name provided");
        }
        if(strlen($this->login) < $this->min){
            $this->setError("Minimum length of a login name is $this->min");
        }
        if(strlen($this->login) > $this->max){
            $this->setError("Maximum length of a login name is $this->max");
        }
        if(!preg_match('/^[a-zA-Z0-9_]+$/', $this->login)){
            $this->setError('Login name contains invalid characters. Only Letters, Numbers and _ are allowed');
        }
    }
}
