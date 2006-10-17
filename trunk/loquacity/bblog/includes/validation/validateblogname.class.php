<?php

class validateblogname extends validator{
    /**
    * Stores the blog name to validate
    *
    * @access private
    * @var string
    */
    var $blog;
    
    /**
    * Minimum length of blog name
    *
    * @access private
    * @var int
    */
    var $min;
    
    /**
    * Maximum length of blog name
    *
    * @access private
    * @var int
    */
    var $max;
    
    function validateblogname($blog=null){
        $this->blog = $blog;
        $this->min = 6;
        $this->max = 255;
        validator::validator();
    }
    function validate(){
        if(is_null($this->blog) || strlen($this->blog) == 0){
            $this->setError("No author name provided");
        }
        if(strlen($this->blog) < $this->min){
            $this->setError("Minimum length of an author name is $this->min");
        }
        if(strlen($this->blog) > $this->max){
            $this->setError("Maximum length of an author name is $this->max");
        }
        if(!preg_match('/^[a-zA-Z0-9_ a]+$/', $this->blog)){
            $this->setError('Blog name contains invalid characters. Only Letters, Numbers and _ are allowed');
        }
    }
}
