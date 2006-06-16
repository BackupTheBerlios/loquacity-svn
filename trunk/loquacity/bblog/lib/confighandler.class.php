<?php

/**
 * Class for loading the configuration from the database
 *
 * @package Loquacity
 * @author Kenneth Power <telcor@users.berlios.de>, http://www.loquacity.info/ - last modified by $LastChangedBy: $
 * @version $Id: $
 * @copyright 2006 Kenneth Power <telcor@users.berlios.de>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */
/* This file is part of Loquacity.
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

class configHandler {

    function configHandler(&$db) {
        $this->_db = $db;
    }
    /**
     * Loads the configuration from the DB and creates constants
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