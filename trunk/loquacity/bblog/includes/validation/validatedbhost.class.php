<?php

class validatedbhost extends validator{
    /**
    * Stores the db host to validate
    *
    * @access private
    * @var string
    */
    var $dbhost;
    
    /**
    * Minimum length of db host
    *
    * @access private
    * @var int
    */
    var $min;
    
    /**
    * Maximum length of db host
    *
    * @access private
    * @var int
    */
    var $max;
    
    function validatedbhost($dbhost=null){
        $this->dbhost = $dbhost;
        $this->min = 6;
        $this->max = 20;
        validator::validator();
    }
    function validate(){
        if(is_null($this->dbhost) || strlen($this->dbhost) == 0){
            $this->setError("No author name provided");
        }
        if(strlen($this->dbhost) < $this->min){
            $this->setError("Minimum length of an author name is $this->min");
        }
        if(strlen($this->dbhost) > $this->max){
            $this->setError("Maximum length of an author name is $this->max");
        }
        if(!preg_match('/^[a-zA-Z0-9_ a]+$/', $this->dbhost)){
            $this->setError('DB Host contains invalid characters. Only Letters, Numbers and _ are allowed');
        }
    }
}
