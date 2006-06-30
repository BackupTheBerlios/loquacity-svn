<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Plugins
 * @author Eaden McKee, http://www.bblog.com
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
 
function identify_function_sectionlinks () {
    $help = '
    <p>Sectionlinks is a Smarty function to be used in templates.
    <p>Example usage
    <ul><li>To create a link list of sections, one per line :<br>
       {sectionlinks}
       <li>To create a link list of sections, seperated by a # <br>
         {sectionlinks sep="#"}
         <li>To make a list with &lt;ul&gt use {sectionlinkd mode="list"}<br />
       <li>Used within a {posts} loop, to link to sections that the post is in, seperated by a commer :<br>
          {sectionlinks sep=", " sections=$post.sections}
    </ul>';

  return array (
    'name'           =>'sectionlinks',
    'type'             =>'function',
    'nicename'     =>'Section Links',
    'description'   =>'Make links to sections',
    'authors'        =>'Eaden McKee <eadz@bblog.com>',
    'licence'         =>'GPL',
    'help'   => $help
  );
}

function smarty_function_sectionlinks($params, &$bBlog) {

    $linkcode = '';

    $mode = (isset($params['mode'])) ? $params['mode'] : 'break';
    $sections = (isset($params['sections'])) ? $params['sections'] : $bBlog->sections;
     

    if($mode=='list') $sep = "";
    else if(!isset($params['sep'])) $sep = "<br />";
    else $sep = $params['sep'];

    $num = count($sections);
    $i=0;
  
    if ($mode=='list') $linkcode .= "<ul>";
    foreach ($sections as $section) {
        $i++;
        //we using arrays in the template and objects in the core..
        $url = $section['url'];
        $nicename = $section['nicename'];
        if($mode=='list')
            $linkcode .= "<li>";
        $linkcode .= '<a href="'.$url.'">'.$nicename.'</a>';

        if($mode=='list')
            $linkcode .= "</li>";
        else if($num > $i)
            $linkcode .= $sep;

    }

    if ($mode=='list') $linkcode .= "</ul>";
    return $linkcode;
}

/* vim: set expandtab: */

?>
