<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Administration
 * @author Kenneth Power <telcor@users.berlios.de>
 * @copyright &copy; 2006 Kenneth Power
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
 * Class managing administrative duties with the database
 */

class DatabaseManager{
	
	/**
	 * Constructor. Loads the appropriate driver class based upon the argument provided
	 *
	 * @param object $db
	 * @param string $driver
	 * @return DatabaseManager
	 */
	function DatabaseManager(&$db, $driver='mysql'){
		$path = dirname(__FILE__).DIRECTORY_SEPARATOR.'dbdrivers';
		return DatabaseManager::loadDriver($db, $path, $driver);
	}
	
	/**
	 * Checks whether driver exists and instantiates an object from it. Returns false on error
	 *
	 * @param string $path Path to dbadmin drivers
	 * @param string $driver
	 * @return mixed
	 */
	function loadDriver(&$db, $path, $driver){
		//Check for the existence of a driver class
		$rval = false;
		$drivers = array();
		if(is_dir($path)){
			$d = dir($path);
			while(($e = $d->read()) !== false){
				if($e !== '.' && $e !== '..'){
					$parts = explode('.', $e);
					if($parts[0] === $driver){
						include_once($path.DIRECTORY_SEPARATOR.$e);
						$classname = $driver.'driver';
						$this->_dbadmin = new $classname($db);
						$this->_dbadmin->_db = $db;
						return $this->_dbadmin;
					}
				}
			}
		}
		return $rval;
	}
}