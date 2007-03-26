<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Plugins
 * @author Reverend Jim <jim@revjim.net>
 * @copyright &copy; 2003  Reverend Jim <jim@revjim.net>
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
 
function identify_function_getarchives () {
$help = '
<p>the {getarchives} function is used to retrieve a list of archives. It takes the following parameters:<br />
<br />
assign: variable to assign data to<br />
sectionid: to request archives only in a certain section<br />
show: can be years, months, days, hours, minutes, or seconds. Determines how detailed the archive list should be<br />
year: requests archives only from a certain year<br />
month: requests archives only from a certain month<br />
day: requests archives only from a certain day<br />
hour: requests archives only from a certain hour<br />
minute: requests archives only from a certain minute<br />
second: requests archives only from a certain second<br />
count: requests a count of the number of entries in each archive (takes longer to compute)<br />';

  return array (
    'name'           =>'getarchives',
    'type'             =>'function',
    'nicename'     =>'GetArchives',
    'description'   =>'Retrieves a list of archives',
    'authors'        =>'Reverend Jim <jim@revjim.net>',
    'licence'         =>'GPL',
    'help'   => $help
  );
}
function smarty_function_getarchives($params, &$loq) {
    $ar = array();
    $opt = $params;

	unset($opt['assign']);

	// If "assign" is not set... we'll establish a default.
	if($params['assign'] == '') {
		$params['assign'] = 'archives';
	}

	$ar = $loq->get_archives($opt);
	
	// No posts.
    if(!is_array($ar)) {
		return;
	}

	$loq->assign($params['assign'],$ar);

  return;
}

?>
