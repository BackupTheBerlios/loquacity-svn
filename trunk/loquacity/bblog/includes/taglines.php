<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Administration
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
 * Random taglines
 *
 * Generates the random slogans in the administration menu
 *
 * @version $Revision$
 */
$taglines = array();
$taglines[] = "In need of a tagline since 2003";
$taglines[] = ":)";
$taglines[] = "Coded from scratch to be cruft free";
$taglines[] = "Smarty based blog software";
$taglines[] = "<sub>Trackbackable comments, threaded commentable trackbacks</sub> : A mouthfull of features";
$taglines[] = "Version ".BBLOG_VERSION;
$taglines[] = "The revolution will be blogged";
$taglines[] = "bBlog has more mmm";
$taglines[] = "Forged in Middle-Earth";
$taglines[] = "So fresh and so clean";
$taglines[] = "Simple, Powerful, Modable, Extenable";
$taglines[] = "The new black";
$taglines[] = "This software never has bugs, it just develops random features";
$taglines[] = "As a computer, I find your faith in technology amusing";
$taglines[] = "Why fork when you can spoon?";
$taglines[] = "Bootylicious blogging";
$taglines[] = "Please support bBlog by <a style='color:#ffffff; font-weight:bolder;' href='http://www.bblog.com/donate.php' target='_blank'>donating</a>";
$tl_n = array_rand($taglines);
$bBlog->assign('tagline',$taglines[$tl_n]);
?>
