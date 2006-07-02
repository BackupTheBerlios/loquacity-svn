<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Administration
 * @author Eaden McKee <email@eadz.co.nz>
 * @copyright &copy; 2003  Eaden McKee <email@eadz.co.nz>
 * @license    http://www.opensource.org/licenses/lgpl-license.php GPL
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
 * charset collection
 *
 * @version $Revision$
 */
//charset collection
$charsets = array(

				/* when adding more charsets just use this stub
				array(
					"short" => '',
					"description" => '',
					"value" =>'',
				),
				*/		

	
				array(
					"short" => 'Unicode UTF-8',
					"description" =>'Unicode UTF-8 (recommended for most languages)',
					"value" =>'UTF-8',
				),
		
				array(
					"short" => 'Unicode UTF-16',
					"description" =>'Unicode UTF-16 (for extended Asian languages)',
					"value" =>'UTF-16',
				),
		
				array(
					"name" => 'None',
					"description" =>'none (not recommended!)',
					"value" =>'',
				),


);//charsets

?>