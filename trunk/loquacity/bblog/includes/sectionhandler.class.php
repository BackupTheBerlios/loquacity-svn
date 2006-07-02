<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Publishing
 * @author Kenneth Power <telcor@users.berlios.de>
 * @copyright &copy; 2006 Kenneth Power
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
 * A class handling sections (for example: add/modify/remove/etc)
 *
 * More detailed description needed. Not finished yet.
 *
 * @version $Revision$
 */

class sectionhandler{
    function sectionhandler(&$db){
        $this->_db = $db;
    }
    function addsection(){
    }
    function modifysection(){
    }
    function removesection(){
    }
    function getallsections(){
        $sects = $this->_db->Execute("SELECT * FROM `".T_SECTIONS."` ORDER BY name");
        if($sects !== false && !$sects->EOF){
             $nsects = array();
             while($sect = $sects->FetchRow()){
                   if(!is_null($sect['sectionid'])){
                       $nsects[$sect['sectionid']] = array(
                           'id'        => $sect['sectionid'],
                           'name'      => stripslashes($sect['name']),
                           'nicename'  => stripslashes($sect['nicename'])
                       );
                   }
             }
             return $nsects;
        }
        else{
            return false;
        }
    }
}
?>
