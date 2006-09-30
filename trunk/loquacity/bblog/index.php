<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Administration
 * @author Eaden McKee <email@eadz.co.nz>
 * @copyright Copyright &copy; 2003  Eaden McKee <email@eadz.co.nz>
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
 * Admistration index page
 *
 * Login page/settings/... - whole adminstration interface
 *
 * @version $Revision$
 */ 

$loggedin = FALSE;
define('IN_BBLOG_ADMIN',TRUE);
// include the config and main code
include_once("config.php");

include_once('includes/charsets.php');

include_once('includes/taglines.php');
// default title
$title = 'Admin';

// make sure the page is never cached - we should probally set no-cache headers also.
$bBlog->setmodifytime(time());

$bBlog->assign_by_ref('title',$title);
// we will store the rss templates in the includes/admin_templates dir, becasue almost noone will need to change them, - reduce clutter in the templates/* directory.
$bBlog->template_dir = LOQ_APP_ROOT.'includes/admin_templates';
$bBlog->compile_id = 'admin';


// check to see if we're not logged in
if(!$bBlog->admin_logged_in()) {
     if(isset($_POST['username']) && isset($_POST['password'])) { // we're trying to log in.
         $loggedin = $bBlog->userauth($_POST['username'],$_POST['password'],TRUE);
     }
}
else{
    $loggedin = TRUE;
}
if((isset($_POST['submit'])) && ($_POST['submit'] == 'Login')){
    $bBlog->assign('tried2login',TRUE);
}
if($loggedin === false) { // we are not logged in! Display the login page
   $menu[0]['url']='index.php';
   $menu[0]['name']='Login';
   $menu[0]['active']=TRUE;
   $bBlog->assign_by_ref('menu',$menu);
   $title= 'Login';
   if($_SERVER['REQUEST_URI'] != $_SERVER['SCRIPT_NAME']) {
   	// tried to go somewhere but was kicked out as session timed out.
	// so when they login we'll redirect them.
   	$bBlog->assign('redirect',base64_encode($_SERVER['REQUEST_URI']));
   }
   $bBlog->display("login.html");
   exit;
}

// seems this could be a reason for the blank page problem
// I think the problem was that redirect was always set after login. Even when the redirect url was ""
if (isset($_REQUEST['redirect']) && strlen($_REQUEST['redirect']) >0) {
	header('Location: '.base64_decode($_REQUEST['redirect']));
	exit;
}

// we're logged in, Hoorah!
// set up the menu

$menu[0]['name']='Post';
$menu[0]['url']='index.php?b=post';
$menu[0]['title']='Post a blog entry';
$bindex['post'] = 0;

$menu[1]['name']='Archives';
$menu[1]['url']='index.php?b=archives';
$menu[1]['title']='Edit past entries and change properties';
$bindex['archives']=1;

$rs = $bBlog->_adb->Execute("select * from ".T_PLUGINS." where type='admin' order by ordervalue");
if($rs !== false && !$rs->EOF){
    $i = 2;
    while($plugin = $rs->FetchRow()){
        $menu[$i]['name'] = $plugin['nicename'];
        $menu[$i]['url']  = 'index.php?b=plugins&amp;p='.$plugin['name'];
        $menu[$i]['title'] = $plugin['description'];
        $pindex[$plugin['name']] = $i;
        $i++;
    }
}

$menu[$i+4]['name'] = 'Captcha';
$menu[$i+4]['url'] = 'index.php?b=captcha';
$menu[$i+4]['title'] = 'Configure and Enable Captcha use';

$menu[$i]['name'] = 'Plugins';
$menu[$i]['url']  = 'index.php?b=plugins';
$menu[$i]['title'] = 'View information about plugins, and scan for new ones.';
$bindex['plugins']=$i;

$menu[$i+1]['name'] = 'Options';
$menu[$i+1]['url']  = 'index.php?b=options';
$menu[$i+1]['title'] = 'Edit imporntant Loquacity options';
$bindex['options']=$i+1;

$menu[$i+2]['name'] = 'About';
$menu[$i+2]['url']  = 'index.php?b=about';
$menu[$i+2]['title'] = 'About Loquacity';
$bindex['about']=$i+2;
$bindex['captcha'] = $i+4;

$menu[$i+3]['name']='Docs';
$menu[$i+3]['url']='http://loquacity.info/documentation/xref/nav.html?index.php.html" target="_blank"'; // NASTY hack!
$menu[$i+3]['title'] = 'Link to the online documentation at Loquacity.info';

$bBlog->assign_by_ref('menu',$menu);

if(isset($_REQUEST['p'])) {
	$menu[$pindex[$_REQUEST['p']]]['active']=TRUE; // now that's an array
} else {
	// Need's a fix here, in the case $_REQUEST['b'] doesn't exists.
	// @ is shut-up mode
	@$m = $bindex[$_REQUEST['b']];
	if($m < 1) $m = 0; // prevent against null values
	@$menu[$m]['active'] = TRUE;

}

if(isset($_GET['b'])) $b = $_GET['b']; else $b = 'post';
if(isset($_POST['b'])) $b = $_POST['b'];

if($b == 'login') $b = 'post'; // the default action when just logged in

switch ($b) {
    case 'post':
    case 'captcha':
    case 'archives':
    case 'options':
    case 'help':
    case 'feedcreator':
    case 'about':
        $title = ucfirst($b);
        if($b === 'post'){
            $title = 'Post Entry';
        }
        if($b === 'about'){
            $title = 'About Loquacity';
        }
        include_once('plugins/builtin.'.$b.'.php');
        break;
    case 'plugins' :
         if (!isset($_GET['p']))    $_GET['p']  = '';
         if (!isset($_POST['p']))   $_POST['p'] = '';

         $title='Plugins';
         include_once('plugins/builtin.plugins.php');
         break;
    case 'logout' :
         $bBlog->admin_logout();
         header('Location: index.php');
         break;

    default :
          $bBlog->assign('errormsg','Unknown b value in admin index.php');
          $title = 'Error';
          $bBlog->display('error.html');
          break;
}
?>