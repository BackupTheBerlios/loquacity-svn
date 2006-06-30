<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Plugins
 * @author Tobias Schlottke
 * @copyright &copy; 2003  Tobias Schlottke, <tschlottke@virtualminds.de>
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
function identify_function_email () {
$help = 'usage: <br/>
        {email email=\'somone@example.com\' name=\'john doe\'} <br/>
        or just<br/>
        {email email=\'somone@example.com\'} <br/>';

  return array (
    'name'           =>'email',
    'type'             =>'function',
    'nicename'     =>'Email',
    'description'   =>'encodes email addresses to get rid of spam bots',
    'authors'        =>'Tobias Schlottke <tschlottke@virtualminds.de>',
    'licence'         =>'GPL',
    'help'   => $help
  );
}
function smarty_function_email($params, &$bBlog) {

    extract($params);
    if(!$name) $name = str_replace("."," dot ",str_replace("@"," at ",$email));
    $email = preg_replace("/\"/","\\\"",$email);
    $old = "document.write('<a href=\"mailto:$email\">$name</a>')";

    $output = "";
    for ($i=0; $i < strlen($old); $i++) {
        $output = $output . '%' . bin2hex(substr($old,$i,1));
    }

    echo "<script language=\"JavaScript\" type=\"text/javascript\">eval(unescape('".$output."'))</script>";
}

