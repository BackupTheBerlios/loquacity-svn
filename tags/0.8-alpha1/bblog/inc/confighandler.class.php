<?php

class configHandler {

    function configHandler(&$db) {
        $this->_db = $db;
    }
    /**
     * Loads teh configuration from the DB and creates constants
     */
    function loadConfig(){
        $rs = $this->_db->Execute('select * from '.T_CONFIG);
        if($rs !== false && !$rs->EOF){
            while($config = $rs->FetchRow()){
                $const_name = 'C_'.$config['name'];
                if (!defined($const_name)) {   
                    define($const_name, $config['value']);
                }
            }
        }
    }
}
?>