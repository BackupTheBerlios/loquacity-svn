<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Administration
 * @author Samir Greadly <xushi.xushi@gmail.com> Eaden McKee <email@eadz.co.nz>
 * @copyright &copy; 2006 Kenneth Power
 * @license    http://www.gnu.org/licenses/gpl.html GPL
 * @source Referenced from http://search.mnogo.ru/doc/msearch-international.html
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
					"short" => 'Group 1 - ISO 8859-1',
					"description" => 'ISO 8859-1 (For Western-European Languages: Albanian, Catalan, Danish, Dutch, English, Faeroese, Finnish, French, Galician, German, Icelandic, Italian, Norwegian, Portuguese, Spanish, Swedish.)',
					"value" =>'ISO-8859-1',
				),
				
				array(
					"short" => 'Group 2 - ISO 8859-2',
					"description" =>'ISO 8859-2 (For Eastern-European Languages: Croatian, Czech, Estonian, Hungarian, Latvian, Lithuanian, Polish, Romanian, Slovak, Slovene.)',
					"value" =>'ISO-8859-2',
				),
				
				array(
					"short" => 'Group 4 - ISO 8859-4',
					"description" =>'ISO 8859-4 (For Baltic Languages)',
					"value" =>'ISO-8859-4',
				),
				
				array(
					"short" => 'Group 5 - ISO 8859-5',
					"description" =>'ISO 8859-5 (For Cyrillic Languages: Bulgarian, Belorussian, Macedonian, Russian, Serbian, Ukrainian.)',
					"value" =>'ISO-8859-5',
				),
				
				array(
					"short" => 'Group 6 - ISO 8859-6',
					"description" =>'ISO 8859-6 (For Arabic)',
					"value" =>'ISO-8859-6',
				),
				
				array(
					"short" => 'Group 7 - ISO 8859-7',
					"description" =>'ISO 8859-7 (For Greek)',
					"value" =>'ISO-8859-7',
				),
				
				array(
					"short" => 'Group 8 - ISO 8859-8',
					"description" =>'ISO 8859-8 (For Hebrew)',
					"value" =>'ISO-8859-8',
				),
				
				array(
					"short" => 'Group 9 - ISO 8859-9',
					"description" =>'ISO 8859-9 (For Turkish)',
					"value" =>'ISO-8859-9',
				),
				
				array(
					"short" => 'Group 101 - ISO-2022-JP',
					"description" =>'ISO-2022-JP (For Japanese)',
					"value" =>'ISO-2022-JP',
				),
				
				array(
					"short" => 'Group 101 - Shift-JIS',
					"description" =>'Shift-JIS (For Japanese)',
					"value" =>'Shift-JIS',
				),
				
				array(
					"short" => 'Group 101 - EUC-JP',
					"description" =>'EUC-JP (For Japanese)',
					"value" =>'EUC-JP',
				),
				
				array(
					"short" => 'Group 102 - GB2312',
					"description" =>' GB2312(For Simplified Chinese, PRC)',
					"value" =>'GB2312',
				),
				
				array(
					"short" => 'Group 103 - Big5',
					"description" =>'Big5 (For Traditional Chinese, ROC)',
					"value" =>'Big5',
				),
				
				array(
					"short" => 'Group 104 - EUC-KR',
					"description" =>'EUC-KR (For Korean)',
					"value" =>'EUC-KR',
				),
				
				array(
					"short" => 'Group 105 - CP874',
					"description" =>'CP874 (For Thai)',
					"value" =>'CP874',
				),
				
				array(
					"short" => 'Group 105 - TIS 620',
					"description" =>'TIS 620 (For Thai)',
					"value" =>'TIS 620',
				),
				
				array(
					"short" => 'Group 105 - MacThai',
					"description" =>'MacThai (For Thai)',
					"value" =>'MacThai',
				),
				
				array(
					"short" => 'Group 106 - CP1258',
					"description" =>'CP1258 (For Vietnamese)',
					"value" =>'CP1258',
				),
				
				array(
					"short" => 'Group 107 - TSCII',
					"description" =>'TSCII (For Indian)',
					"value" =>'TSCII',
				),
				
				array(
					"short" => 'Group 107 - MacGujarati',
					"description" =>'MacGujarati (For Indian - MAC)',
					"value" =>'MacGujarati',
				),
				
				array(
					"short" => 'Group 108 - geostd8',
					"description" =>'geostd8 (For Georgian)',
					"value" =>'geostd8',
				),
		
				array(
					"name" => 'None',
					"description" =>'none (not recommended!)',
					"value" =>'',
				),


);//charsets

?>