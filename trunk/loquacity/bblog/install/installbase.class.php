<?php

/**
* The base class inherited by each installer step
*/

class installbase extends Smarty{
	function installbase(){
        stringHandler::removeMagicQuotes($_POST);
        
        Smarty::Smarty();
        $this->assign('version', LOQ_CUR_VERSION);
        $this->template_dir = LOQ_INSTALLER.'/templates';
        $this->compile_dir = ini_get("session.save_path");
        $this->__init();
	}
    function display(){
        if(isset($this->errors) && count($this->errors) > 0){
            $this->assign('errors', $this->errors);
            parent::display($this->error_template);
        }
        else{
            parent::display($this->template);
        }
    }
}
