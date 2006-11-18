<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Plugins
 * @author Sebastian Werner
 * @copyright &copy; 2003  Sebastian Werner
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
 * smarty modifier to set locale
 */
function smarty_modifier_locale($stream, $locale) {
  setlocale("LC_ALL", $locale);
  setlocale("LC_TIME", $locale);
  setlocale("LC_LANG", $locale);

  return $stream;
}

function identify_modifier_locale () {
  return array (
    'name'           =>'locale',
    'type'           =>'modifier',
    'nicename'       =>'Set Locale',
    'description'    =>'Set locale and return unmodified input data',
    'authors'         =>'Sebastian Werner',
    'licence'         =>'GPL'
  );
}

function bblog_modifier_locale_help () {
?>
<p>Set Locale</p>
<pre>
{$post.posttime|locale:"de_DE"|data_format:"date"}
</pre>
<?php
}
?>
