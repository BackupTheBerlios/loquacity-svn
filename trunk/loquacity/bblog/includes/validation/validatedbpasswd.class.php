<?php

class validatedbpasswd extends validator{
    /**
    * Stores the db password to validate
    *
    * @access private
    * @var string
    */
    var $dbpasswd;
    
    /**
    * Minimum length of db password
    *
    * @access private
    * @var int
    */
    var $min;
    
    /**
    * Maximum length of db password
    *
    * @access private
    * @var int
    */
    var $max;
    
    function validatedbpasswd($dbpasswd=null){
        $this->dbpasswd = $dbpasswd;
        $this->min = 6;
        $this->max = 20;
        validator::validator();
    }
    function validate(){
        if(is_null($this->dbpasswd) || strlen($this->dbpasswd) == 0){
            $this->setError("No author name provided");
        }
        if(strlen($this->dbpasswd) < $this->min){
            $this->setError("Minimum length of an author name is $this->min");
        }
        if(strlen($this->dbpasswd) > $this->max){
            $this->setError("Maximum length of an author name is $this->max");
        }
        if(!preg_match('/^[a-zA-Z0-9_ a]+$/', $this->dbpasswd)){
            $this->setError('DB User Password contains invalid characters. Only Letters, Numbers and _ are allowed');
        }
    }
}
