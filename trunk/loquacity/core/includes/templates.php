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
 * Deals with templating functions
 *
 * @version $Revision$
 */                                                                        

////
// !Custom Smarty template handler for use with database templates
function db_get_template ($tpl_name, &$tpl_source, &$smarty_obj) {
    Global $loq;
    $rs = $loq->_adb->Execute("select template from ".T_TEMPLATES." where templatename='$tpl_id'");
    if($rs !== false && !$rs->EOF){
        $tpl_source = $rs->fields[0];
    }
    return true;
}

////
// !Get the timestamp of a template from the database
function db_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj) {
    // do database call here to populate $tpl_timestamp.
    Global $loq;
    $rs = $loq->_adb->Execute("select compiletime from ".T_TEMPLATES." where templatename='$tpl_id'");
    if($rs !== flase && !$rs->EOF){
        $tpl_timestamp = $rs->fields[0];
    }
    return true;
}

function db_get_secure($tpl_name, &$smarty_obj) {
    // assume all templates are secure
    return true;
}

function db_get_trusted($tpl_name, &$smarty_obj){ }// not used


////
// !Make a footer in the html comments
// Make footer containing the page generation time
// and number of database calls and last modified date
function buildfoot() {
	global $loq;
    $mtime = explode(" ",microtime());
	$endtime = $mtime[1] + $mtime[0];
	
	$pagetime = round($endtime - $loq->begintime,5);
	$foot = "
<!--//
This page took $pagetime seconds to make
Powered by Loquacity : http://www.loquacity.info/
//-->";
	return $foot;
}

?>
