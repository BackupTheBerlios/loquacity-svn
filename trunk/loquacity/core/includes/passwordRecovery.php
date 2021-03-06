<?php
session_start();
/**
 * passwordRecovery.php - Password retrieval
 *
 * Will check if a username exists, ask it's secret question and reset/email the password.
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

include_once("../config.php");
include_once('charsets.php');
include_once('taglines.php');
require_once("validation/passwordManager.class.php");

$myPasswdMgr =& new passwordManager($loq->_adb);
$loq->template_dir = LOQ_APP_ROOT.'includes/admin_templates';
$loq->assign('sidemsg', 'Loquacity Password Recovery');


$_SESSION['username'] = $_POST['username'];
$_SESSION['answer'] = $_POST['answer'];

// if a username in the post is entered, and that username exists in the database, 
if((isset($_SESSION['username'])) && ($_SESSION['username'] == checkUsername($_SESSION['username']))) {
	// get the secret question for the user
	$secQuestion = $myPasswdMgr->getQuestion($_SESSION['username']);
		
	$loq->assign('question', $secQuestion);
	$_SESSION['answer'] = $_POST['answer'];
	$template = 'askquestion.html';
	
	// Now check if we have an answer or not, and compare them.
	// psudo: if (checkAnswers(pw1,pw2)  where pw1 = getAnswer(username)
	if( $myPasswdMgr->checkAnswers($myPasswdMgr->getAnswer($_SESSION['username']), $_SESSION['answer']) ) {
			// success! reset password and send the email.
			setPassword($_SESSION['username'], $_SESSION['answer']);
			sendEmail($user, $email, $passwd);
			$template = 'status.html';
	}
	else {
		$loq->assign('title', 'Please answer your question');
		$template = 'askquestion.html';
	}
}
else {
	$loq->assign('title', 'Please enter your Loquacity username');
	$template = 'getusername.html';
}	

	
	
	
	




function setPassword($user, $passwd) {
		global $myPasswdMgr;
		/** 
		 * I could have just said passwordManager::setPassword($user, stringHandler::toSHA1(passwordManager::randomWord(5))); 
		 * but that way I'll have no idea what the unhashed password is, and
		 * won't be able to send it to the user.
		*/
		//generate new password and write it to the db. setPassword(user, password)
		$passwd = $myPasswdMgr->randomWord(5);
		$myPasswdMgr->setPassword($user, passwordManager::toSHA1($passwd));
}


function checkUsername($user) {
	global $myPasswdMgr;
	//if (validateauthorname::validateauthorname($user)) {
	// I'm just checking if the user has an email address for now.
	// Will write a check user function too asap.
	if ($myPasswdMgr->getEmail($user)) {
		return true;
	}
	else {
		return false;
	}
	
}



function sendEmail($user, $email, $passwd) {
	require_once('LoquacityMailer.class.php');
	global $myPasswdMgr;
	global $loq;
	$mail = new LoquacityMailer;
	
	
	 // Now, send an email to the user with the new (unhashed) password
	$mail->AddAddress($email, $user);
	$mail->Subject = "Loquacity password recovery.";
	$mail->From = $loq->_adb->GetOne("SELECT value FROM " . T_CONFIG . " WHERE name='email'");
	$mail->Reciever = $user;
	$mail->Body = "Dear " . $mail->Reciever.",

Thank you for using Loquacity.
You have requested for your password to be sent to you via email.
If you did not, then please reset your secret answer/question immediately.

Your username is : " . $user . "
Your new password is: ". $passwd ."

Click on the link below and make sure to change the password immediately.
TODO: link to his blog...

This is an automatic message. Please do not reply to it. For further enquiries, visit the forum at 
http://forum.loquacity.info

Remember, the Loquacity team will NEVER ask you for your password, so do NOT give it away to anyone.

Thank you,
The Loquacity Team.";

// Send email, or display an error.
		if(!$mail->Send()) {
			$mail->error_handler("Description: ... some description.\n");
			$template = 'error.html';
		}
		else {
		$loq->assign('result', 'The email was sent successfully. You should receive your new password shortly.');
		$template = 'status.html';
		}
}


$loq->display($template);

?>
