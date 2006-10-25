<?php

class validatesecretanswer extends validator{
    /**
    * Stores the secret answer to validate
    *
    * @access private
    * @var string
    */
    var $answer;
    
    /**
    * Minimum length of secret answer
    *
    * @access private
    * @var int
    */
    var $min;
    
    /**
    * Maximum length of secret answer
    *
    * @access private
    * @var int
    */
    var $max;
    
    function validatesecretanswer($answer=null){
        $this->answer = $answer;
        $this->min = 6;
        $this->max = 60;
        validator::validator();
    }
    function validate(){
        if(is_null($this->answer) || strlen($this->answer) == 0){
            $this->setError("No author name provided");
        }
        if(strlen($this->answer) < $this->min){
            $this->setError("Minimum length of an author name is $this->min");
        }
        if(strlen($this->answer) > $this->max){
            $this->setError("Maximum length of an author name is $this->max");
        }
        if(!preg_match('/^[a-zA-Z0-9_ a]+$/', $this->answer)){
            $this->setError('Secret Answer contains invalid characters. Only Letters, Numbers and _ are allowed');
        }
    }
}
