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

class BackupManager{
	/**
	 * Constructor - builds an instance of BackupManager
	 *
	 * @param object $db ADODB Instance
	 * @return BackupManager
	 */
	function BackupManager(&$db){
		$this->_db = $db;
		$this->_store = LOQ_APP_ROOT.'generated'.DIRECTORY_SEPARATOR.'backup';
		$this->_enabled = $this->checkStore();
	}
	/**
	 * Creates a new backup
	 *
	 * @param string $type Accepts 'upgrade' or 'backup'
	 * @return bool
	 */
	function backup($type){
		if(!$this->_enabled){
			return false;
		}
		$tmp_file = tempnam($this->_store, $type.'_');
		$sql_file = $this->_store.DIRECTORY_SEPARATOR.'backup.sql';
		touch($tmp_file);
		if(($fh = fopen($tmp_file, 'w+b')) !== false){
			if(flock($fh, LOCK_EX)){
				$dbm = new DatabaseManager($this->_db);
				$dbm->backup($fh);
				fclose($fh);
				chmod($tmp_file, 0666);
				if(is_file($sql_file)){
					unlink($sql_file);
				}
				rename($tmp_file, $sql_file);
				$this->compress($type);
				if($this->checkArchive()){
					unlink($sql_file);
				}
			}
		}
	}
	/**
	 * Restores the selected backup
	 *
	 * @param string $backup
	 * @return bool
	 */
	function restore($backup){
		if(!$this->_enabled){
			return false;
		}
	}
	/**
	 * Returns a list of existing backups
	 *
	 * @return array
	 */
	function listBackups(){
		if(!$this->_enabled){
			return false;
		}
		$backups = array();
		$d = dir($this->_store);
		while(($entry = $d->read()) !== false){
			if($entry !== '.' && $entry !== '..'){
				if(strstr($entry, 'upgrade_') !== false){
					$backups['upgrade'][] = $entry;
				}
				else{
					$backups['backup'][] = $entry;
				}
			}
		}
		sort($backups['upgrade'], SORT_STRING);
		sort($backups['backup'], SORT_STRING);
		$d->close();
		return $backups;
	}
	/**
	 * Informs caller whether backup storage is usable
	 *
	 * @return bool
	 */
	function checkStore(){
		if(is_dir($this->_store) && is_writable($this->_store)){
			//try to write to it
			return touch($this->_store.DIRECTORY_SEPARATOR.'test');
		}
		else{
			return false;
		}
	}
	/**
	 * Creates the compressed archive
	 * 
	 * @param string $type
	 * @return void
	 */
	function compress($type){
		if(function_exists('gzopen')){
			include_once(LOQ_APP_ROOT.'3rdparty'.DIRECTORY_SEPARATOR.'pclzip'.DIRECTORY_SEPARATOR.'pclzip.lib.php');
			$today = date('Y_m_d');
			$record = $this->nextCount($today, $type) + 1;
			$this->_new_backup = $this->_store.DIRECTORY_SEPARATOR.$type.'_'.$today.'_'.$record.'.zip';
			$zip = new PclZip($this->_new_backup);
			if(($zip->create($this->_store.'/backup.sql,'.LOQ_APP_ROOT.'config.php', PCLZIP_OPT_REMOVE_ALL_PATH)) == 0 ){
				$this->errors[] = $zip->errorInfo(true);
			}
		}
	}
	/**
	 * Basic checking of archive integrity
	 *
	 * @return bool
	 */
	function checkArchive(){
		$rval =false;
		if(file_exists($this->_new_backup) && filesize($this->_new_backup) > 0){
			include_once(LOQ_APP_ROOT.'3rdparty'.DIRECTORY_SEPARATOR.'pclzip'.DIRECTORY_SEPARATOR.'pclzip.lib.php');
			$zip = new PclZip($this->_new_backup);
			if(($list = $zip->listContent()) !== 0 && count($list) == 2){
				$rval = true;
			}
		}
		return $rval;
	}
	/**
	 * Returns the next entry number used in the record field of the backup name
	 * 
	 * When given a date in the format of yyyy_mm_dd and a type of 'upgrade' or 'backup',
	 * the entries in the backup storage area are scanned. The sum of files named $type_$today' is
	 * returned as the current record.
	 *
	 * @param date $today
	 * @param string $type
	 * @return int
	 */
	function nextCount($today, $type){
		$d = dir($this->_store);
		$files = 0;
		while(($entry = $d->read()) !== false){
			if($entry !== '.' && $entry !== '..'){
				if(strstr($entry, 'backup_'.$today)){
					$files++;
				}
			}
		}
		$d->close();
		return $files;
	}
}
?>