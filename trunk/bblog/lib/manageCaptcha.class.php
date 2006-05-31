<?php

class manageCaptcha{
    function manageCaptcha(&$db){
        $this->_db = $db;
        //$this->_db->debug = true;
    }
    
    /**
    * Retrieves the current captcha configuration
    *
    * @return array Associative array of values
    */
    function getCurrentSettings(){
        $sql = 'SELECT `name`, `value` from `'.T_CONFIG.'` WHERE `name` LIKE "CAPTCHA_%"';
        return $this->_db->GetAll($sql);
    }
    
    /**
    * Saves the configuration to the database
    *
    * @param array $vars Associative array
    */
    function saveConfiguration($vars){
        foreach($vars as $key => $val){
            $sql = 'UPDATE `'.T_CONFIG.'` SET `value`='.$this->_db->quote($val).' WHERE `name` = "'.$key.'"';
            $this->_db->Execute($sql);
        }
    }
}
?>
