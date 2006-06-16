<?php

/**
* sectionhandler.class.php
*
* Copyright 2006 Kenneth Power <kenneth.power@gmail.com>
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version. 
*
* This program is distributed in the hope that it will be useful, 
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
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
