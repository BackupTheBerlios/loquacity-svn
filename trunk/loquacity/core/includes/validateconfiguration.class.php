<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Configuration
 * @author Kenneth Power <telcor@users.berlios.de>
 * @copyright Copyright &copy; 2006 Kenneth Power
 * @license    http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.loquacity.info
 * @since 0.8-alpha2
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
* Performs validation on all configuration items
*
* Validation is performed by a series of classes based upon the Validator
* class. Each user-supplied configuration item is validated and sanitized
* by an appropriate class. One class per configuration item.
*/

include_once(LOQ_APP_ROOT.'includes/validator.class.php');

class validateconfiguration{
    
    /**
    * Stores the errors that occur
    */
    var $errors = array();
    
    /**
    * Includes and instantiates each Validator class then calls ::valdiate()
    */
    function validateconfiguration(){
        foreach(array('validatelogin', 'validatepassword') as $class){
            include_once(LOQ_APP_ROOT."includes/validation/$class.class.php");
        }
        $this->config = array(
            'login_name' => new validatelogin($_POST['login_name']),
            'login_password' => new validatepassword($_POST['login_password'], $_POST['verify_password'])
            );
        $this->storeCurrent();
        $this->validate();
    }
    /**
	* Performs the checks and retrieves the errors
	*/
    function validate(){
        foreach($this->config as $key=>$val){
            if(!$val->isValid()){
                $this->errors[$key] = $val->errors;
            }
        }
    }
    /**
	* Reports whether errors occurred
	*
	* @return bool
	*/
    function isValid(){
        return (count($this->errors) > 0) ? false : true;
    }
    
    /**
	* Makes a copy of the user data for temporary storage
	*/
    function storeCurrent(){
        foreach(array('blog_name', 'blog_description', 'author_name', 'login_name', 'login_password', 'verify_password', 'secret_question', 'secret_answer', 'email_address', 'db_username', 'db_password', 'db_database', 'db_host', 'blog_url', 'fs_path') as $setting){
            if(isset($_POST[$setting]) && strlen($_POST[$setting]) > 0){
                $_SESSION['config'][$setting] = $_POST[$setting];
            }
        }
        //Not mandatory, but set if defined
        if(isset($_POST['table_prefix'])){
            $_SESSION['config']['table_prefix'] = $_POST['table_prefix'];
        }
    }
}