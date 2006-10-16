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
* The first step of Installation and Upgrades.
* Perform some basic system checks to ensure minimum requirements
* are satisified.
*
* Method __init calls these tests and performs the appropriate error handling.
*/
class installprescan extends installbase{


    /**
    * URI contained defines next step in Installer
    *
    * @var string
    * @access private
    */
    var $form_action;
    
    /**
    * The template to display
    *
    * @var string
    * @access private
    */
    var $template;
    
	function installprescan(){
        $this->form_action = 'install.php?install=install';
        $this->template = 'configuration.html';
        installbase::installbase();
	}

    function __init(){
        $this->checkwritable();
        $this->loadconfiguration();
        if(isset($this->errors) && count($this->errors) > 0){
            $this->assign("errors", $this->errors);
            $this->assign("prescan_errors", true);
        }
        $this->assign('form_action', $this->form_action);
    }


	/**
     * Check if the needed files and folders are writable.
	 * 
	 * @TODO should the file and foldernames be predefined as they are now, or passed as params?
     *
    */
	function checkwritable() {
		foreach(array('generated', 'generated/cache', 'generated/templates', 'generated/cache/favorites.xml', 'config.php') as $target){
			if(!is_writable(LOQ_APP_ROOT.$target))
				$this->errors[] = "$target is not writable";
        }
    }

	/**
     * Check if both passwords match or not.
     *
	 * @param string $passwd1 the password
	 * @param string $passwd2 the password entered again
     * @return bool
    */
	function checkPasswordFields($passwd1, $passwd2) {
		//@todo add check for password fields
		 if ($passwd1 !== $passwd2){
			return false;
		}
	}
}
