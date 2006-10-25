<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Plugins
 * @author Mario Delgado <mario@seraphworks.com>
 * @copyright &copy; 2003  Mario Delgado <mario@seraphworks.com>
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
 * A smarty function for displaying Loquacity links
 */

function identify_function_links () {
$help = '
<p>Links is a Smarty function to be used in templates.
<p>Example usage
<ul><li>To return a list of links, one per line :<br>
   {links}</li>
   <li>To return a list, seperated by a # <br>
     {links sep=#}</li>
   <li>To return a list for the category humor <br>
     {links cat=humor}</li>
   <li>To return a list without the category stores <br>
     {links notcat=stores}</li>
   <li>To limit the number of links returned <br>
     {links num=5}</li>
   <li>The default behavior is to return the list in <br>
    or as set in the admin panel. This <br>
     can be changed with one of the following key words <br>
    <br>
     {links ord=nicename} <br>
     {links ord=category}</li>
   <li>To return a list in descending order <br>
     {links desc=TRUE}</li>
   <li>cat and notcat are mutually exclusive and cannot <br>
     be used together. They can both be used with sep, <br>
     ord, num and desc which can all be used together. <br>
     Category names and ord key words are case sensative.</li>
</ul>';

  return array (
    'name'             =>'links',
    'type'             =>'function',
    'nicename'         =>'Links',
    'description'      =>'Make a list of links',
    'authors'          =>'Mario Delgado <mario@seraphworks.com>',
    'licence'          =>'GPL',
    'help'             => $help
  );


}

function smarty_function_links($params, &$loq) {

    $markedlinks = '';

    if(!isset($params['sep'])) {
       $sep = "<br />";
    } else {
       $sep = $params['sep'];
    }
    
    if(isset($params['presep'])) $presep = $params['presep']; // use this for lists

    if(isset($params['desc'])) {
       $asde = "DESC";
    } else {
       $asde = "";
    }

    if(isset($params['ord'])) {
       $order = $params['ord'];
    } else {
       $order = "position";
    }

    if(isset($params['num'])) {
       $max = $params['num'];
    } else {
       $max = "20";
    }
    $db = $loq->_adb;
    if(isset($params['cat'])) {
        $rs = $db->Execute("select categoryid from ".T_CATEGORIES." where name='".$params['cat']."'");
        if($rs !== false && !$rs->EOF){
            $cat = $rs->fields[0];
        }
    }

    if(isset($params['notcat'])) {
        $rs = $db->Execute("select categoryid from ".T_CATEGORIES." where name='".$params['notcat']."'");
        if($rs !== false && $rs->EOF){
            $notcat = $rs->fields[0];
        }
    }

    $sql = 'SELECT nicename, url FROM `'.T_LINKS.'` ';
    if($cat){
        $sql .=' WHERE category='.$cat;
    }
    if($notcat){
        $sql .=' WHERE category!='.$notcat;
    }
    $sql .=' ORDER BY '.$order.' '.$asde.' LIMIT '.$max;
    $links = $db->Execute($sql);
    if($links !== false && !$links->EOF){
        while($l = $links->FetchRow()){
            $markedlinks .= $presep.'<a href="'.$l['url'].'">'.$l['nicename'].'</a>'.$sep;
      }
    }
    return $markedlinks;
}

?>