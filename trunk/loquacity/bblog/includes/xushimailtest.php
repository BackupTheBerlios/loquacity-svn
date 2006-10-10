 <?php
/**
 * xushimailtest.php
 *
 * This is just a test file to check that everything's working. When run, it should
 * use your localhost's mail system to send an email to the email address.
 * Judge yee not by thee content.. as i said, it's just a small test.
 *
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (C) 2006 Kenneth Power <telcor@users.berlios.de>
 *
 * @package Loquacity
 * @subpackage Mail
 * @author Samir Greadly <xushi.xushi@gmail.com>
 * @copyright &copy; 2006 Kenneth Power
 * @source phpmailer - <http://phpmailer.sourceforge.net/>
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

require_once("LoquacityMailer.class.php");

// Instantiate your new class
$mail = new LoquacityMailer;


	 // Now, set some data
        $mail->AddAddress("xushi.xushi@gmail.com");
        $mail->Subject = "test email.";
        $mail->Body = "Dear john";

  	
        // Send email, or display an error.
        if(!$mail->Send())
        {
            echo "There was an error sending the message.";
            exit;
        }

	// Success!
        echo "Message was sent successfully.<br /><br /> You should recieve an email containing your new password shortly.";

?>
