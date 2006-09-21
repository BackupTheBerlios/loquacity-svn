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
        }
        parent::display($this->template);
    }
    function loadconfiguration(){
        if(!isset($_SESSION['config'])){
             // provide some useful defaults, and prevents undefined indexes.
             $this->assign('fs_path', LOQ_APP_ROOT);
             $this->assign('db_host', 'localhost');
             $host = $_SERVER['HTTP_HOST'].substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '/install.php')).'/';
             //Some configurations don't set the protocol
             if(stripos($host, 'http://') === false){
                 $host = 'http://'. $host;
             }
             $this->assign('blog_url', $host);
             $this->assign('table_prefix', 'loq_');
        }
        else{
            foreach($_SESSION['config'] as $name=>$value){
                $this->assign($name, $value);
            }
        }
    }
}
