<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Comments
 * @author Eaden McKee <email@eadz.co.nz>
 * @copyright &copy; 2003  Eaden McKee <email@eadz.co.nz>
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

/**
 * send notifications and the like
 *
 * @version $Revision$
 */
// first lets set some defaults

define('MAIL_HEADER','
Greetings,
You are recieving this notification because you have chosen to recieve notifications from '.C_BLOGNAME.'.

');

define('MAIL_FOOTER','

Regards,
'.C_BLOGNAME.',
'.C_BLOGURL.'
');

define('MAIL_FROM','"'.htmlspecialchars(C_BLOGNAME).'"'.' <'.C_EMAIL.'>');

// function to notify the owner about a new comment or post. 
function notify_owner($subject,$message) { 
	// do they want notifications?
	if(C_NOTIFY == 'true') { 
		mail(C_EMAIL,$subject,MAIL_HEADER.$message.MAIL_FOOTER,
						"Content-Type: text/plain; charset=".C_CHARSET."\r\n".
						"From: ".MAIL_FROM."\r\n".
						"Errors-To: ".MAIL_FROM."\r\n".
						"Content-Type: text/plain; charset=".C_CHARSET."\r\n"
				);

	}

}

// function to notify the poster that a reply has been posted to their comment. 
function notify_poster ($to,$subject,$message) {
 // not yet implemented.

}
?>
