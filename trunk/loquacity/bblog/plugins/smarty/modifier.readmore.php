<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Plugins
 * @author Tim Lucas <t.lucas-toolmantim.com>
 * @copyright &copy; 2003  Tim Lucas <t.lucas-toolmantim.com>
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
 
function smarty_modifier_readmore($text, $postid, $readmoretext="read more",$wordcount=true) {

    $PREG_TAG = '/<!--\s*(\/?read\s*more:?[^-]*)\s*-->/';
    $PREG_READMORE_START = '/^read\s*more/';
    $PREG_READMORE_END = '/^\s*\/read\s*more\s*/';    

    global $loq;
    $link = $loq->_get_entry_permalink($postid);

    $textar = preg_split($PREG_TAG,$text,-1,PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
    $text = "";

    $cuttingout = false;
    $i = -1;

    foreach ( $textar as $textbit ) {
      $i++;
      $textbit = trim($textbit);

      if ( preg_match($PREG_READMORE_START,$textbit) ) {

        // check for nested cuts
        if($cuttingout)
          continue;

        $text .= '<a href="'.$link.'">';
        // fix default string
        if( !strpos($textbit,':') ) {
          $text .= $readmoretext;
        } else {
          $text .= substr($textbit,strpos($textbit,':')+1);
        }
        $text .= '</a>';
    
        // print wordcount
        if($wordcount) {
          $text .= '&nbsp;<em>('.count(explode(' ',$textar[$i+1])).' words)</em>';
        }

        $cuttingout = true;
        continue;
      }
      else if ( preg_match($PREG_READMORE_END,$textbit) ) {
        $cuttingout = false;
        continue;
      }
      else if (!$cuttingout) {
        $text.=$textbit;
      }

    } // end foreach

  return $text;
}

function identify_modifier_readmore () {
  return array (
    'name'           =>'readmore',
    'type'           =>'smarty_modifier',
    'nicename'       =>'Read More',
    'description'    =>'Chops a post short with a readmore link',
    'authors'        =>'Tim Lucas <t.lucas-toolmantim.com>',
    'licence'        =>'GPL',
    'help'	     =>'Usage:<br>
<p>Use the readmore modifier on the {$post.body} tag, to cut off text at the HTML
comment &lt;!-- readmore --&gt; .</p>
<p>There are 4 parameters, <strong>post id</strong> (number), <strong>default text</strong> (string), 
<strong>word count</strong> (true/false) and <strong>word count text</strong> (string).</p>
<p><i>postid</i> is the id of the post (used to create the link) - required.</p>
<p><i>default text</i> is the default text used for the readmore link (default is "Read more") - optional.</p>
<p><i>word count</i> is a toggle to print the word count of the cutout text - optional.</p>
<p><i>word count text</i> allows you to localise the word count (default is "words") - optional.</p>
<p>You can cut sections of text by using &lt;!-- readmore --&gt; in conjuction
with &lt!-- /readmore --&gt;.
<p><strong>Example template usage:</strong></p>
<p>{$post.body|readmore:$post.postid}</p>
<p>{$post.body|readmore:$post.postid:"Read more..."}</p>
<p>{$post.body|readmore:$post.postid:"Read more...":false}</p>
<p>{$post.body|readmore:$post.postid:"keep on reading":true:"whispers"}</p>
<p><strong>Example post usage:</strong></p>
<p><i>Cutting the post off and using the default readmore text</i>:</p>
<p>My amazing story<br/>
&lt;!-- readmore --&gt;<br/>
of my amazing story... it\'s amaaazing</p>
<p><i>Cutting a section of text out and replacing with own text link</i>:</p>
<p>My amazing story<br/>
&lt;!-- readmore: read it now! --&gt;<br/>
This is my hidden story<br/>
&lt;!-- /readmore --&gt;<br/>
And back to the post again.</p>');
}

?>
