<?php

class validateauthorname extends validator{
    /**
    * Stores the author name to validate
    *
    * @access private
    * @var string
    */
    var $aname;
    
    /**
    * Minimum length of author names
    *
    * @access private
    * @var int
    */
    var $min;
    
    /**
    * Maximum length of author name
    *
    * @access private
    * @var int
    */
    var $max;
    
    function validateauthorname($aname=null){
        $this->aname = $aname;
        $this->min = 4;
        $this->max = 50;
        validator::validator();
    }
    function validate(){
        if(is_null($this->aname) || strlen($this->aname) == 0){
            $this->setError("No author name provided");
        }
        if(strlen($this->aname) < $this->min){
            $this->setError("Minimum length of an author name is $this->min");
        }
        if(strlen($this->aname) > $this->max){
            $this->setError("Maximum length of an author name is $this->max");
        }
        if(!preg_match('/^[a-zA-Z0-9_ ]+$/', $this->aname)){
            $this->setError('Author name contains invalid characters. Only Letters, Numbers and _ are allowed');
        }
    }
}
