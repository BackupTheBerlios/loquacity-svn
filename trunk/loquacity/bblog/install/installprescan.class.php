<?php

class installprescan extends installbase{
	function installprescan(){
		$con = get_parent_class(__CLASS__);
		parent::$con();
        $this->form_action = 'install.php?install=install';
        $this->template = 'configuration.html';
        $this->error_template = '';
	}
    function __init(){
        $this->checkwritable();
        $this->loadconfiguration();
    }
    function checkwritable() {
        foreach(array('generated', 'generated/cache', 'generated/templates', 'generated/cache/favorites.xml', 'config.php') as $target){
            if(!is_writable(LOQ_APP_ROOT.$target))
                $this->errors[] = "$target is not writable";
        }
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
