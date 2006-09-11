<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Installer
 * @author Kenneth Power http://blog.tel-cor.com
 * @copyright &copy; 2006 Kenneth Power <telcor@users.berlios.de>
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

 
 // provide some useful defaults, and prevents undefined indexes.
if(!isset($config['path'])) $config['path'] = dirname(__FILE__).'/';
if(!isset($config['url'])) $config['url'] = 'http://'.$_SERVER['HTTP_HOST'].str_replace('bblog/install.php','',$_SERVER['REQUEST_URI']);
if(!isset($config['author'])) $config['author'] = 'admin';
if(!isset($config['table_prefix'])) $config['table_prefix'] = 'loq_';
if(!isset($config['password'])) $config['password'] = "";
if(!isset($config['passwd_verify'])) $config['passwd_verify'] = "";
if(!isset($config['email'])) $config['email'] = "";
if(!isset($config['real_name'])) $config['real_name'] = "";
if(!isset($config['db_host'])) $config['db_host'] = 'localhost';
if(!isset($config['db_username'])) $config['db_username'] = "";
if(!isset($config['db_password'])) $config['db_password'] = "";
if(!isset($config['db_database'])) $config['db_database'] = "";
if(!isset($config['blogname'])) $config['blogname'] = "";
if(!isset($config['blogdescription'])) $config['blogdescription'] = "";

$config['version'] = LOQ_CUR_VERSION;
?>
