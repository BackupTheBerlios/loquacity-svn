<?php

class installprescan extends installbase{
	function installprescan(){
        $this->form_action = 'install.php?install=install';
        $this->template = 'configuration.html';
        $this->error_template = '';
        installbase::installbase();
	}
    function __init(){
        $this->checkwritable();
        $this->loadconfiguration();
        if(isset($this->errors) && count($this->errors) > 0){
            $this->template = '';
        }
        else{
            $this->assign('form_action', $this->form_action);
        }
    }
    function checkwritable() {
        foreach(array('generated', 'generated/cache', 'generated/templates', 'generated/cache/favorites.xml', 'config.php') as $target){
            if(!is_writable(LOQ_APP_ROOT.$target))
                $this->errors[] = "$target is not writable";
        }
    }
}
