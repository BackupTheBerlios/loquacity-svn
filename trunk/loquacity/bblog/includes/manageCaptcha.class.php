<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Comments
 * @author Kenneth Power <telcor@users.berlios.de>
 * @copyright &copy; 2006 Kenneth Power
 * @license    http://www.opensource.org/licenses/lgpl-license.php GPL
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
 * Class for getting the captcha settings from the database and saving them
 *
 * @version $Revision$
 */

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
