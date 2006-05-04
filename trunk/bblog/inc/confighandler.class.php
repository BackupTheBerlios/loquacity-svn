<?php

class configHandler {

    function configHandler(&$db) {
        $this->_db = $db;
    }
    function loadConfig(){
        // get config from the database
        $config_rows = $this->_db->get_results('select * from '.T_CONFIG);
        // loop through and define the config
        foreach($config_rows as $config_row) {
            $const_name = 'C_'.$config_row->name;
            if (!defined($const_name)) {   
                define($const_name, $config_row->value);
            }
        }
    }
}
?>