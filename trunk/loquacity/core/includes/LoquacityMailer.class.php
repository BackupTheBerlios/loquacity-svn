<?php
/**
 * LoquacityMailer.class.php - extends the PHPMailer class
 *
 * Here's a class that extends the PHPMailer class
 * and sets the defaults for our Loquacity site.
 * This saves us the trouble of hacking the original phpMaier,
 * to ease upgrading and make our life easier :)
 *
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (C) 2006 Kenneth Power <telcor@users.berlios.de>
 *
 * @package Loquacity
 * @subpackage Mail
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


define('LOQ_APP_ROOT', dirname(dirname(__FILE__)));
require_once(LOQ_APP_ROOT.'/3rdparty/phpmailer/class.phpmailer.php');


class LoquacityMailer extends PHPMailer {

    // Set default variables for all new objects

    /**
     	* @TODO i'd rather have $Host - or even, all the details - be read from the database, instead of writing it here. Just incase any robot would come or any evil penguin wants to hack this file for spam reasons.
     */
    var $From     = "loquacityMailer@loquacity.info";
    var $FromName = "The Loquacity Team";
    var $Host     = "localhost"; 			//add more, seperated by a ; colon. eg. "localhost;mysite.com"
    var $Mailer   = "smtp";                         // Alternative to IsSMTP()
    var $WordWrap = 75;
    var $Reciever = ".";


	/**
 	 * Replace the default error_handler
     * @param string $msg
     * @return void
     */
	/**
	 * @TODO we could link this handler to a main centralised error handler for Loq.
	*/	
    function error_handler($msg) {
        print("Loquacity Mailer Error");
        print("There was an error sending the message.<br /><br />");
        printf("%s", $msg);
        exit;
    }
}
?>
