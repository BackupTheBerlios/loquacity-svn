<?php

class validateblogurl extends validator{
    /**
    * Stores the blog url to validate
    *
    * @access private
    * @var string
    */
    var $blogurl;
    
    /**
    * Minimum length of blogurl
    *
    * @access private
    * @var int
    */
    var $min;
    
    function validateblogurl($blogurl=null){
        $this->blogurl = $blogurl;
        $this->min = 6;
        $this->max = 50;
        validator::validator();
    }
    function validate(){
        if(is_null($this->blogurl) || strlen($this->blogurl) == 0){
            $this->setError("No author name provided");
        }
        if(strlen($this->blogurl) < $this->min){
            $this->setError("Minimum length of an author name is $this->min");
        }
        if(!preg_match('/^[a-zA-Z0-9_ a]+$/', $this->blogurl)){
            $this->setError('Blog Url contains invalid characters. Only Letters, Numbers and _ are allowed');
        }
    }
}
