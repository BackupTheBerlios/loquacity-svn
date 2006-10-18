<?php
session_start();
/**
 * lostpuser.php - Password retrieval
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


$myPasswdMgr =& new passwordManager($bBlog->_adb);



$bBlog->template_dir = LOQ_APP_ROOT.'includes/admin_templates';
$_SESSION['isuser'] = 0;



// Start with getting the username
$bBlog->assign('sidemsg', 'Loquacity Password Recovery');
$bBlog->assign('title', 'Please enter your Loquacity username');



$template = 'getusername.html';
$user = $_POST['username'];

// check if a username is entered. If yes, check if it's in the database.
if($user) {
	
	//check if the email exists (and - more or less - hence if the user exists)
	$email = $myPasswdMgr->getEmail($user);


	if ($email) {
		//get the secret question
		$secQuestion = $myPasswdMgr->getQuestion($user);
		
		$bBlog->assign('question', $secQuestion);
		$template = 'askquestion.html';
	}
	else {
		// stick this all in an error function
        $bBlog->assign('errormsg', 'Sorry. Please visit the Loquacity forum for more advise.');
		$template = 'error.html';
		echo 'errrrrrrrrrrrrrrrror';
	}
	
	// Now check if we have an answer or not, and compare them
	if(checkAnswers($user)) { 

		/** 
		 * I could have just said passwordManager::setPassword($user, stringHandler::toSHA1(passwordManager::randomWord(5))); 
		 * but that way I'll have no idea what the unhashed password is, and
		 * won't be able to send it to the user.
		*/
		//generate new password and write it to the db. setPassword(user, password)
		$passwd = $myPasswdMgr->randomWord(5);
		$myPasswdMgr->setPassword($user, passwordManager::toSHA1($passwd));

		// create email
		createEmail($user, $email, $passwd);
		$bBlog->assign('result', 'The email was sent successfully. You should receive your new password shortly.');
		$template = 'status.html';
	}
	else {
		$bBlog->assign('errormsg', 'Error, invalid answer. Recovery aborted.');
		$template = 'error.html';
	}	

}
else {
		$bBlog->assign('errormsg', 'Error: Please enter a valid username');
		$template = 'error.html';
		echo 'userrrrrrrrrrrrrrrrrrr';
}





		


function checkAnswers($user)
{
	global $mail;
	global $myPasswdMgr;


	// get the answer from db
	$secAnswer = $myPasswdMgr->getAnswer($user);
	$userAnswer = $_POST['pass'];

	// test if they're the same
	if($secAnswer == $userAnswer)
	{
		return 1;
	}
	else
	{
		return 0;
	}
}

function createEmail($user, $email, $passwd) {
	require_once('LoquacityMailer.class.php');
	global $myPasswdMgr;
	global $bBlog;
	$mail = new LoquacityMailer;
	
	
	 // Now, send an email to the user with the new (unhashed) password
	$mail->AddAddress($email, $user);
	$mail->Subject = "Loquacity password recovery.";
	$mail->From = $bBlog->_adb->GetOne("SELECT value FROM " . T_CONFIG . " WHERE name='email'");
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
	if(!$mail->Send())
	{
	$mail->error_handler("Description: ... some description.\n");
	exit;
	}
}

$bBlog->display($template);

?>
