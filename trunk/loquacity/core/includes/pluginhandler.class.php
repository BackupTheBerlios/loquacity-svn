<?php

/**
 * pluginhandler.class.php - Loquacity Plugin Handler
 * pluginhandler.class.php - author: Kenneth Power <telcor@users.berlios.de>
 * based on code by Eaden McKee <email@eadz.co.nz>
 *
 * @package Loquacity
 * @subpackage Core
 * @author Kenneth Power <telcor@users.berlios.de>, Eaden McKee <email@eadz.co.nz>
 * @copyright &copy; 2006 Kenneth Power, &copy; 2003 Eaden McKee
 * @license    http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.loquacity.info
 * @since 0.8-alpha2
 *
 * Loquacity http://www.loquacity.info/
 * Copyright (C) 2006 Kenneth Power <telcor@users.berlios.de>
 * Copyright (C) 2003  Eaden McKee <email@eadz.co.nz>
 *
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
 
class pluginhandler{
    function pluginhandler(&$db){
        $this->_db = $db;
    }
    function scan_for_plugins($dir) {
        $currentPlugins = $this->currentPlugins();
        $scanned = $this->scanPluginDir($dir);
        $installed = 0;
        foreach($scanned as $type => $typeList){
            foreach($typeList as $ind=>$member){
                
                if((!isset($currentPlugins[$type])) || (isset($currentPlugins[$type]) && !in_array($member, $currentPlugins[$type]))){
                    if($this->installPlugin($type, $member)){
                        $installed++;
                    }
                }
            }
        }
        return $installed;
    }
        
    function installPlugin($type, $plugin){
        $loader = 'identify_'.$type.'_'.$plugin;
        if(function_exists($loader)){
            $newplugin = $loader();
            return $this->_db->AutoExecute(T_PLUGINS, $newplugin, 'INSERT', $magicq=get_magic_quotes_gpc());
        }
        return false;
    }
    function scanPluginDir($dir){
        $pluginList = array();
        $dh = opendir($dir);
        while((($file = readdir( $dh )) !== false )){
            if($file === 'smarty'){
                $list1 = $this->scanPluginDir($dir . DIRECTORY_SEPARATOR . $file);
                foreach($list1 as $type => $plugins){
                    if(isset($pluginList[$type]) && is_array($pluginList[$type])){
                        array_merge($pluginList[$type],  $plugins);
                    }
                    else{
                        $pluginList[$type] = $plugins;
                    }
                }
            }
            else if(substr($file, -3) == 'php'){
                $parts = explode('.',$file);
                if($parts[0] !== 'builtin'){
                    if($this->isValidPlugin($dir, $file)){
                        $pluginList[$parts[0]][] = $parts[1];
                    }
                }
            }
        }
        closedir( $dh );
        return $pluginList;
    }
    
    function currentPlugins(){
        $current = array();
        $rs = $this->_db->Execute("select type, name from ".T_PLUGINS);
        if($rs !== false && !$rs->EOF){
                while($plugin = $rs->FetchRow()){
                        $current[$plugin['type']][] = $plugin['name'];
                }
            }
        return $current;
    }
    
    function isValidPlugin($dir, $file){
        $parts = explode('.', $file);
        if(file_exists($dir. DIRECTORY_SEPARATOR . $file)){
            include_once($dir. DIRECTORY_SEPARATOR . $file);
            $loader = 'identify_'.$parts[0].'_'.$parts[1];
            if(function_exists($loader)){
                return true;
            }
        }
        return false;
    }
}
