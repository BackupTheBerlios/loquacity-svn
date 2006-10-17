<?php

class validateblogdescription extends validator{
    /**
    * Stores the blog name to validate
    *
    * @access private
    * @var string
    */
    var $blogDescription;
    
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
    
    function validateblogdescription($bdesc=null){
        $this->blogDescription = $bdesc;
        $this->min = 6;
        $this->max = 255;
        validator::validator();
    }
    function validate(){
        if(is_null($this->blogDescription) || strlen($this->blogDescription) == 0){
            $this->setError("No blog description provided");
        }
        if(strlen($this->blogDescription) < $this->min){
            $this->setError("Minimum length of a blog description is $this->min");
        }
        if(strlen($this->blogDescription) > $this->max){
            $this->setError("Maximum length of an blog description is $this->max");
        }
        if(!preg_match('/^[a-zA-Z0-9_ a]+$/', $this->blogDescription)){
            $this->setError('Blog Description contains invalid characters. Only Letters, Numbers and _ are allowed');
        }
    }
}
