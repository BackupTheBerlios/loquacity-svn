<?php

class validatefspath extends validator{
    /**
	* Stores the filesystem path to validate
	*
	* @access private
	* @var string
	*/
    var $fspath;
  
    
    function validatefspath($fspath=null){
        $this->fspath = $fspath;
        validator::validator();
    }
    function validate(){
        if(is_null($this->fspath) || strlen($this->fspath) == 0){
            $this->setError("No filesystem path provided");
        }
        if(strlen($this->fspath) < $this->min){
            $this->setError("Minimum length of an author name is $this->min");
        }
        if(!preg_match('/^[a-zA-Z0-9_ a]+$/', $this->fspath)){
            $this->setError('Blog Url contains invalid characters. Only Letters, Numbers and _ are allowed');
        }
    }
}
