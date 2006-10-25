<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Plugins
 * @author Elie `LordWo` BLETON <lordwo_REM_OVE_THIS@laposte.net>
 * @copyright &copy; 2003  Elie `LordWo` BLETON <lordwo_REM_OVE_THIS@laposte.net>
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

function identify_function_getcontent () {
$help = '
<p>the {getcontent} function is used to retrieve the content page linked to the section.<br /><br />
Your index.html template should include() the result of this function, or proceed with normal blog display if the result is FALSE.</p>
';
  return array (
    'name'           =>'getcontent',
    'type'           =>'function',
    'nicename'       =>'GetContent',
    'description'    =>'Returns the content page linked to the section. Return FALSE if none.<br>This',
    'authors'        =>'Elie `LordWo` BLETON <lordwo_REM_OVE_THIS@laposte.net>',
    'licence'        =>'GPL',
    'help'           => $help
  );
}

function smarty_function_getcontent($params, &$loq) {

  // Retrieving data
  $loq->get_sections();
  $sections = $loq->sections;
  foreach ($sections as $object) {
     $new[$object->sectionid] = $object;
  }
  $sections = $new;
  
  $current_section = $loq->get_template_vars("sectionid");
    
  // Return  
  $loq->assign("content",$sections[$current_section]->content);
}

?>
