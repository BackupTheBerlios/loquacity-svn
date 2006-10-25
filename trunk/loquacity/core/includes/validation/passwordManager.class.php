<?php
/**
 * passwordManager.class.php - password management.
 *
 * This class should manage the obtaining, resetting, and
 * passing of passwords from and to the DB.
 * 
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (C) 2006 Kenneth Power <telcor@users.berlios.de>
 *
 * @package Loquacity
 * @subpackage Security
 * @author Samir Greadly <xushi.xushi@gmail.com>
 * @copyright &copy; 2006 Kenneth Power
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
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

class passwordManager {

	// temporary until proper username is implemented.
	var $username = "demo";

	/**
	 * Constructor to pass the connection into the class.
	 * @param object $connection The connection to the DB
	 * @return void
	*/
	function passwordManager(&$db) {
		$this->_adb =& $db;
	}

	/**
	 * update author's table with new password.
	 * @param var $enc_password The new encrypted password
	 * @return void
	*/
	function setPassword($username, $enc_password) {
		$this->_adb->Execute("UPDATE ".T_AUTHORS." SET password='".$enc_password."' WHERE id='1'");
	}

	/**
	 * Get the user's password
	 *
	 * @param var $user The user name
	 * @return var $passwd The hashed password
	*/
	function getPasswd($user) {
		$passwd = $this->_adb->GetOne("SELECT password FROM ".T_AUTHORS." WHERE nickname='$user'");
		return $passwd;
	}

	/**
	 * Generate a new random word with $len length.
	 *
	 * @author huhu - <http://www.phpfreaks.com/quickcode/Random_Password_Generator/56.php>
	 * @param integer $len Length of new password
	 * @return var $pass
    */
	function randomWord($len)
	{
		$data = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		$max = strlen( $data ) - 1 ;
		$pass = '' ;
		for( $i=0; $i<$len; $i++ ) {
			$pass .= substr( $data, mt_rand(0, $max), 1 ) ;
		}
		return $pass ;
	}

	/**
	 * Get the email of a specific user
	 *
	 * @param var $user The user name
	 * @return var $email The email address
	*/
	function getEmail($user) {
		$email = $this->_adb->GetOne("SELECT email FROM ".T_AUTHORS." WHERE nickname='$user'");
		return $email;
	}


	/**
	 * Get the secret question from the user
	 *
	 * @param var $user The user name
	 * @return var $secQuestion The email address
	*/
	function getQuestion($user) {
		$secQuestion = $this->_adb->GetOne("SELECT secret_question FROM ".T_AUTHORS." WHERE nickname='$user'");
		return $secQuestion;
	}

	/**
	 * Get the secret answer from the user
	 *
	 * @param var $user The user name
	 * @return var $secAnswer The email address
	*/
	function getAnswer($user) {
		$secAnswer = $this->_adb->GetOne("select secret_answer from ".T_AUTHORS." where nickname='$user'");
		return $secAnswer;
	}

	/**
     * Hash a string, typically a password, using the MD5 hashing algorithm
     *
     * @param string $str
     * @return mixed
     */
	function toMD5($str) {
		return md5($str);
	}

	/**
     * Hash a string, typically a password, using the SHA1 hashing algorithm
     *
     * @param string $str
     * @return mixed
     */
	function toSHA1($str) {
		return sha1($str);
	}

/**
	 * Check both strings and return true if match.
	 *
	 * @param var $str1 String 1
	 * @param var $str2 String 2
	 * @return bool	true/false.
	*/
	function checkAnswers($str1, $str2) {
		($str1 == $str2)?true:false;
	}

}
?>
