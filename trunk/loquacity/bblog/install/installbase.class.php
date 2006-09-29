<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Installation
 * @author Kenneth Power <telcor@users.berlios.de>
 * @copyright Copyright &copy; 2006 Kenneth Power
 * @license    http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.loquacity.info
 * @since 0.8-alpha1
 *
 * LICENSE:
 *
 * This file is part of Loquacity.
 *
 * Loquacity is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Loquacity is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Loquacity; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


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
