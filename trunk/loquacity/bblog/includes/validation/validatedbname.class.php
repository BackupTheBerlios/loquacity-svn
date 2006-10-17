<?php

/**
* Validates the given database name matches the specified criteria
*
* This criteria is gleaned from the MySQL manual. When moving cross-db
* we'll need to compare with other databases to see whether changes need
* made to this class.
*
*/
class validatedbname extends validator{
    /**
    * Stores the db name to validate
    *
    * @access private
    * @var string
    */
    var $dbname;
    
    /**
    * Minimum length of db name
    *
    * @access private
    * @var int
    */
    var $min;
    
    /**
    * Maximum length of db name
    *
    * @access private
    * @var int
    */
    var $max;
    
    function validatedbname($dbname=null){
        $this->dbname = $dbname;
        $this->min = 6;
        $this->max = 64;
        validator::validator();
    }
    function validate(){
        if(is_null($this->dbname) || strlen($this->dbname) == 0){
            $this->setError("No db name provided");
        }
        if(strlen($this->dbname) < $this->min){
            $this->setError("Minimum length of a db name is $this->min");
        }
        if(strlen($this->dbname) > $this->max){
            $this->setError("Maximum length of a db name is $this->max");
        }
        if(strrpos($this->dbname, ' ') == strlen($this->dbname)){
            $this->dbname = trim($this->dbname);
        }
        if(!preg_match('/^[a-zA-Z0-9_\$]+$/', $this->dbname)){
            $this->setError('DB Name contains invalid characters. Only Letters, Numbers, _ and $ are allowed');
        }
    }
}
