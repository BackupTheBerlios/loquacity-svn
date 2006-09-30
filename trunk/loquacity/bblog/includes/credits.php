<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Administration
 * @author Samir Greadly <xushi.xushi@gmail.com>, Eaden McKee <email@eadz.co.nz>
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
 * contains a list of the developers of bBlog
 *
 * @version $Revision$
 */
// make it an array for more flexable layout
$credits = array(
"				---------- ----------",
"				 The Admin / Dev Team",
"				---------- ----------",
" ",
"Telcor - <a href='http://blog.tel-cor.com'>Kenneth Power</a> - Project Manager, Lead Developer",
"Bazzle - <a href='http://www.loquacity.info'>Mark Whitney</a> -  Project Developer",
"Gremeth - <a href='http://www.loquacity.info'>Myke Stubbs</a> - Project Developer",
"Stan - <a href='http://www.loquacity.info'>Stanley Schade</a> - Project Developer",
"Titanium Geek - <a href='http://www.loquacity.info'>Alison Bain</a> - Project Developer",
"Xushi - <a href='http://xushi.co.uk'>Samir Greadly</a> - Code Design, Commentary, Documentation.",
"... To be edited asap.. not sure who is who exacly, yet.",
" ",

"				---------- ----------",
"				    Previous bBlog Developers",
"				---------- ----------",
" ",
'Eadz - <a href="http://www.eadz.co.nz/" target=_blank>Eaden McKee</a>, bBlog core code / project founder.',
"Hijacker - <a href='http://sjuengling.xetronet.de/' target=_blank>Sascha J?ngling</a> - Project Coordinator, documentation, bug fixing",
"Xushi - <a href='http://xushi.co.uk' target=_blank>Samir Greadly</a> - Project Admin, Forum maintanance, Bug fixes, added features, testing, code enhancement",
"Telcor - <a href='http://blog.tel-cor.com' target=_blank>Kenneth Power</a> - Coding, bug fixes, testing",
"Clever - <a href='http://blog.johnnaked.com' target=_blank>Edbury Raymond Enegren IV</a> - Project Support, Testing, plugin implementation and hacking",
"Wormeyman - <a href='http://www.wormeyman.com/' target=_blank>Eric Johnson</a> - Wiki Maintenance and spam removal, minor code fixing, bug reporting.",
"Javaducky - <a href='http://www.javaducky.com' target=_blank>Paul Balogh</a> - Plugins and features.",
"Kleppa - <a href='http://www.asdf.com' target=_blank>Anders Klepaker</a> - Bug fixing, plugin development",
"Tobius - <a href='http://www.tobymiller.com/' target=_blank>Toby Miller</a> - Bug fixing, plugin development",
"Woe - <a href='http://woe-labs.net' target=_blank>Elie Bleton</a> - core programming and plugin hacking.",
" ",

"				---------- ----------",
"				    Previous bBlog Members",
"				---------- ----------",
" ",
"Pixelpope - <a href='http://pixelpope.com' target=_blank>Dominic Frohlof</a> - Multilingual & RTL/LTR support, bug fixing, features, testing.",

"DL8 - <a href='http://webbi.cheetux.org.il'>Nir Lavi</a> - Bug fixes, testing.",

"Archives funtions and various other various bits by <a href='http://www.revjim.net/' target='_blank'>Rev Jim</a>, core dev team",

"<a href='http://www.trendwhores.de/'>Tobias Schlottke</a> - core dev team",

"<a href='http://www.lwo-lab.net/'>Elie Bleton</a> - core dev team",

"Better recentposts plugin and admin interface design among other things by <a href='http://www.sebastian-werner.net/' target='_blank'>Sebastian Werner</a>, core dev team",

"<a href='http://www.toolmantim.com/' target='_blank'>Tim Lucas</a> for actually knowing regex and making a wicked readmore plugin, among other things, core dev team",

"Messju Mohr (Smarty Author), helping out with smarty issues",

"Database class (ez_sql) by  Justin Vincent",

"Smarty code by the smart folks at <a href='http://smarty.php.net/' target='_blank'>smarty.php.net</a>",

"Mario Delgado - Links Plugin",

"<a href='http://www.keithdevens.com/software/xmlrpc/'>Keith Devens - XML-RPC</a>",

"Martin Konicek - RSS Fetcher Plugin",

"<a href='mailto:kellan@protest.net'>Kellan Elliott-McCrea</a> - <a href='http://magpierss.sourceforge.net' target='_blank'>MagpieRSS</a>, used in RSS Fetcher Plugin",

"textile and the since() function  by <a href='http://www.textism.com/' target='_blank'>Dean Allen</a>",

"blogroll plugin based on code from <a href='http://www.philringnalda.com/phpblogroll/' target='_blank'>Phil Ringnalda's blogroll</a>",

"<a href='http://glutnix.webfroot.co.nz/'>Brett Taylor</a> - Feedback and beta testing",

"Tanel Raja - New calendar plugin",

"Ulf Harnhammar - <a href='http://sf.net/projects/kses/' target='_blank'>KSES</a> - HTML/XHTML filter that only allows some elements and attributes",

"Some http header and bookmarklet code from b2/<a href='http://www.wordpress.org/' target='_blank'>wordpress</a>",

"bbcode smarty modifier by Andr? Rabold , with make_clickable by Nathan Codding
and documentation from <a href='http://www.phpbb.com/' target='_blank'>PHPBB</a>",

"Admin post hidden options thingy and readmore plugin inspired by the one on <a  href='http://www.livejournal.com/' target='_blank'>LiveJournal</a>",

"Calendar Javascript inspired by the calendar that comes with pivotlog",

"Lines theme from from <a href='http://www.movablestyle.com/'>Movable Style</a>, Released under the GPL by <a href='http://scott.yang.id.au/'>Scott Yang</a>",

"MT API support, <a href='http://www.bluewire.net.nz/'>Mark Rowe</a>",

"Icons from <a href='http://gtmcknight.com/buttons/index.php' target='_blank'>Steal These Buttons</a>",
"Thomas Reynolds <thomasr@infograph.com>, atom fixes"
);
?>
