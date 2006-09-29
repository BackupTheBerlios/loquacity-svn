<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Administration
 * @author wordpress (http://wordpress.org), b2
 * @copyright Copyright &copy; 2006 wordpress
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
 * Adding a bookmark to a browser
 *
 * probably adds a bookmark to your browser, borrowed from wordpress
 *
 * @version $Revision$
 */
// this all borrowed from wordpress(.org), but I think most of this came from b2
# browser detection
$is_lynx = 0; $is_gecko = 0; $is_winIE = 0; $is_macIE = 0; $is_opera = 0; $is_NS4 = 0;
if (!isset($HTTP_USER_AGENT)) {	
	$HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
}
if (preg_match('/Lynx/', $HTTP_USER_AGENT)) {
	$is_lynx = 1;
} elseif (preg_match('/Gecko/', $HTTP_USER_AGENT)) {
	$is_gecko = 1;
} elseif ((preg_match('/MSIE/', $HTTP_USER_AGENT)) && (preg_match('/Win/', $HTTP_USER_AGENT))) {
	$is_winIE = 1;
} elseif ((preg_match('/MSIE/', $HTTP_USER_AGENT)) && (preg_match('/Mac/', $HTTP_USER_AGENT))) {
	$is_macIE = 1;
} elseif (preg_match('/Opera/', $HTTP_USER_AGENT)) {
	$is_opera = 1;
} elseif ((preg_match('/Nav/', $HTTP_USER_AGENT) ) || (preg_match('/Mozilla\/4\./', $HTTP_USER_AGENT))) {
	$is_NS4 = 1;
}
$is_IE    = (($is_macIE) || ($is_winIE));
# browser-specific javascript corrections
$wp_macIE_correction['in'] = array(
	'/\%uFFD4/', '/\%uFFD5/', '/\%uFFD2/', '/\%uFFD3/',
	'/\%uFFA5/', '/\%uFFD0/', '/\%uFFD1/', '/\%uFFBD/',
	'/\%uFF83%uFFC0/', '/\%uFF83%uFFC1/', '/\%uFF83%uFFC6/', '/\%uFF83%uFFC9/',
	'/\%uFFB9/', '/\%uFF81%uFF8C/', '/\%uFF81%uFF8D/', '/\%uFF81%uFFDA/',
	'/\%uFFDB/'
);
$wp_macIE_correction['out'] = array(
	'&lsquo;', '&rsquo;', '&ldquo;', '&rdquo;',
	'&bull;', '&ndash;', '&mdash;', '&Omega;',
	'&beta;', '&gamma;', '&theta;', '&lambda;',
	'&pi;', '&prime;', '&Prime;', '&ang;',
	'&euro;'
);
$wp_gecko_correction['in'] = array(
	'/\‘/', '/\’/', '/\“/', '/\�?/',
	'/\•/', '/\–/', '/\—/', '/\Ω/',
	'/\β/', '/\γ/', '/\θ/', '/\λ/',
	'/\π/', '/\′/', '/\″/', '/\�/',
	'/\€/', '/\ /'
);
$wp_gecko_correction['out'] = array(
	'&8216;', '&rsquo;', '&ldquo;', '&rdquo;',
	'&bull;', '&ndash;', '&mdash;', '&Omega;',
	'&beta;', '&gamma;', '&theta;', '&lambda;',
	'&pi;', '&prime;', '&Prime;', '&ang;',
	'&euro;', '&#8201;'
);

?>
<p>You can drag the following link to your links bar or add it to your bookmarks and when you "Press it" it will open up a popup window with information and a link to the site you're currently browsing so you can make a quick post about it. Try it out:</p>
<p>

<?php
$bookmarklet_height= 460;
$siteurl = LOQ_APP_URL;
$blogname= 'bBlog';
if ($is_NS4 || $is_gecko) {
?>
    <a href="javascript:Q=document.selection?document.selection.createRange().text:document.getSelection();void(window.open('<?php echo $siteurl; ?>index.php?b=post&popup=true&text='+escape(Q)+'<?php echo $bookmarklet_tbpb ?>&popupurl='+escape(location.href)+'&popuptitle='+escape(document.title),'WordPress bookmarklet','scrollbars=yes,width=600,height=<?php echo $bookmarklet_height ?>,left=100,top=150,status=yes'));">Press It
    - <?php echo $blogname ?></a>
    <?php
} else if ($is_winIE) {
?>
    <a href="javascript:Q='';if(top.frames.length==0)Q=document.selection.createRange().text;void(btw=window.open('<?php echo $siteurl ?>index.php?b=post&popup=true&text='+escape(Q)+'<?php echo $bookmarklet_tbpb ?>&popupurl='+escape(location.href)+'&popuptitle='+escape(document.title),'bookmarklet','scrollbars=yes,width=600,height=<?php echo $bookmarklet_height ?>,left=100,top=150,status=yes'));btw.focus();">Press it
    - <?php echo $blogname ?></a>
    <script type="text/javascript" language="JavaScript">
<!--
function oneclickbookmarklet(blah) {
	window.open ("profile.php?action=IErightclick", "oneclickbookmarklet", "width=500, height=450, location=0, menubar=0, resizable=0, scrollbars=1, status=1, titlebar=0, toolbar=0, screenX=120, left=120, screenY=120, top=120");
}
// -->
</script>
    <br />
    <br />
    One-click bookmarklet:<br />
    <a href="javascript:oneclickbookmarklet(0);">click here</a>
    <?php
} else if ($is_opera) {
?>
    <a href="javascript:void(window.open('<?php echo $siteurl ?>index.php?b=post&popup=true&popupurl='+escape(location.href)+'&popuptitle='+escape(document.title)+'<?php echo $bookmarklet_tbpb ?>','bookmarklet','scrollbars=yes,width=600,height=<?php echo $bookmarklet_height ?>,left=100,top=150,status=yes'));">Press it
    - <?php echo $blogname ?></a>
    <?php
} else if ($is_macIE) {
?>
    <a href="javascript:Q='';if(top.frames.length==0);void(btw=window.open('<?php echo $siteurl ?>index.php?b=post&popup=true&text='+escape(document.getSelection())+'&popupurl='+escape(location.href)+'&popuptitle='+escape(document.title)+'<?php echo $bookmarklet_tbpb ?>','bookmarklet','scrollbars=yes,width=600,height=<?php echo $bookmarklet_height ?>,left=100,top=150,status=yes'));btw.focus();">Press it
    - <?php echo $blogname ?></a> 
    <?php
}
?>
</p>

