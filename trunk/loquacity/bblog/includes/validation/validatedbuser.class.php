<?php

/**
* Validates the database user name against the specified criteria
*
* The criteria is gleaned from the MySQL manual.
*/
class validatedbuser extends validator{
    /**
    * Stores the db user name to validate
    *
    * @access private
    * @var string
    */
    var $dbuser;
    
    /**
    * Minimum length of db username
    *
    * @access private
    * @var int
    */
    var $min;
    
    /**
    * Maximum length of dn username
    *
    * @access private
    * @var int
    */
    var $max;
    
    function validatedbuser($dbuser=null){
        $this->dbuser = $dbuser;
        $this->min = 6;
        $this->max = 16;
        validator::validator();
    }
    function validate(){
        if(is_null($this->dbuser) || strlen($this->dbuser) == 0){
            $this->setError("No author name provided");
        }
        if(strlen($this->dbuser) < $this->min){
            $this->setError("Minimum length of an author name is $this->min");
        }
        if(strlen($this->dbuser) > $this->max){
            $this->setError("Maximum length of an author name is $this->max");
        }
        if(!preg_match('/^[a-zA-Z0-9_ a]+$/', $this->dbuser)){
            $this->setError('DB User Name contains invalid characters. Only Letters, Numbers and _ are allowed');
        }
    }
}
