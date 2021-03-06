<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Comments
 * @author Kenneth Power <telcor@users.berlios.de>, Demian Turner
 * @copyright &copy; 2006 Kenneth Power, Demian Turner
 * @license    http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.loquacity.info
 * @since 0.8-alpha2
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
 * Class for handling string related functions
 *
 * A pseudo static class, it never needs instantiated. This class
 * serves to centralize various string handling functions, such as
 * transforming typed hyperlinks into clickable links.
 * Some code is borrowed from the Seagull project, according to the terms of the
 * BSD license. Such code is:
 * Copyright (c) 2006, Demian Turner
 *
 * @version $Revision$
 */ 
class stringHandler{
    /**
    * Converts typed links into clickable links
    *
    * The following URI patterns are searched, and replaced with
    * URIs prepended with the Google Redirector. Additionally, if
    * a URI is not already in an Anchor (&lt;a&gt;) tag, we wrap
    * it with one. For URIs that already have anchor tags, all
    * attributes (style="", title="", etc) are saved.
    * Patterns:
    *   &lt;a href="www.example.com"&gt;Example&lt;/a&gt;
    *   &lt;a href="http://www.example.com"&gt;Example&lt;/a&gt;
    *   &lt;a href="ftp://www.example.com"&gt;Example&lt;/a&gt;
    *   &lt;a href="https://www.example.com"&gt;Example&lt;/a&gt;
    *   http://www.example.com
    *   https://www.example.com
    *   ftp://ftp.example.com
    *   ftp.example.com
    *   www.really.long.example.com
    *   All URIs with a query path:
    *   www.example.com?example=yes
    *
    * @param string $str String to check and convert
    * @return string
    */
    function transformLinks($str){
        // Prepend a blank to the string, otherwise the regexes will not find
        // a match at the beginning of a string.
        //([^"\']+[\w_.\s]*)
        //$str = ' '.$str;
        $et_pattern = '/<a(.*?)href[\s]*=[\s]*["\'](.*?)(["\'][^>]*[\s]*)>([^<]+[.\s]*)<\/a>/xmsi';
        $prot_pattern = '/^([fh]+[t]{0,1}tp[s]*:\/\/([\w_~#=&%\/\:;@,\.\?\+\-]+))/xmsi';
        $simple_pattern = '/^(www|ftp)\.([\w_~#=&%\/\:;@,\.\?\+\-]+)/xmsi';
        $patterns = array($et_pattern, $prot_pattern, $simple_pattern);

        $et_replace = '<a$1href="http://www.google.com/url?sa=D&q=$2$3>$4</a>';
        $prot_replace = '<a href="http://www.google.com/url?sa=D&q=$1">$2</a>';
        $simple_replace = '<a href="http://www.google.com/url?sa=D&q=http://$0">$0</a>';
        $repl = array($et_replace, $prot_replace, $simple_replace);

        //Since the regex replace above adds a space within the HREF attribute, we need to remove it
        ksort($repl);
        ksort($patterns);
        $str = preg_replace($patterns, $repl, $str);

        $str = str_replace('sa=D&q=http:// ', 'sa=D&q=http://', $str);

        //Return the result, removing the prepended space
        return $str; //substr($str, 1);
    }
    /**
    * Remove HTML tags from a string
    *
    * This is merely a wrapper around the native strip_tags function
    *
    * @param string $str String to remove tags from
    * @param array $tags Optional. List of tags to allow
    * @return string
    */
    function removeTags($str, $tags=''){
        return (strlen($tags) > 0) ? strip_tags($str, $tags) : strip_tags($str);
    }
    /**
    * Reports whether a string contains hyperlinks
    *
    * @param string $str The string to check
    * @return bool
    */
    function containsLinks($str){
        $rval = false;
        $str = strtolower($str);
        if(strpos($str, 'href') !== false)
            $rval = true;
        if(strpos($str, 'http') !== false)
            $rval = true;
        if(strpos($str, 'www.') !== false)
            $rval = true;
        if(strpos($str, 'ftp.') !== false)
            $rval = true;
        if(strpos($str, 'ftp:') !== false)
            $rval = true;
        if(strpos($str, 'mailto') !== false)
            $rval = true;
        return $rval;
    }

    /**
    * Simple check for presence of a protocol
    *
    * Checks a string for the http:// and ftp:// protocol
    * qualifiers
    *
    * @param string $str
    * @return bool
    */
    function isProtocolPresent($str){
        $str = strtolower($str);
        $rval = false;
        if(strpos($str, 'http://') !== false)
            $rval = true;
        if(strpos($str, 'ftp://') !== false)
            $rval = true;
        return $rval;
    }

    /**
    * Reports whether a string contains clickable links
    *
    * @param string $str The string to check
    * @return bool
    */
    function containsTransformedLinks($str){
        $rval = false;
        $str = strtolower($str);
        if(strpos($str, '<a') !== false)
            $rval = true;
        return $rval;
    }

     /**
     * Reports whether a link contains the Google Redirector.
     * This is meant to be used after a string successfully passes the containsTransformedLinks
     * check. If used without first passing that check unexpected results may return.
     *
     * @param string $str The link to check
     * return bool True if contains the link redirector, False if it does not
     */
    function containsRedirector($str){
        $rval = false;
        $str = strtolower($str);
        if(strpos($str, 'http://www.google.com/url?sa=D&q=') !== false)
            $rval = true;
        return $rval;
    }

    /**
    * Replace various characters with their HTML entities equivalent
    *
    * @param string $str The string to parse
    * @return string
    */
    function encodeHTML($str){
        $result = $str;
        $search = array('&', '<', '>', '"', '&lt;a');
        $replace = array('&amp;', '&lt;', '&gt;', '&quot;');
        $result = str_replace($search, $replace, $result);
        return $result;
    }

    /**
    * Reports whether a string contains links external to the blog/domain
    */
    function containsExternalLinks($str){
        $str = $str;
        return true;
    }

    /**
    * Prepends the Google redirector service to the HREF attribute of anchor tags
    * Use this when a hyperlink already exists as a HTML tag
    *
    * @param string $str
    * @return string
    */
    function redirectHref($str){
        // Google link redirector
        return preg_replace("/href=\"/i","href=\"http://www.google.com/url?sa=D&q=", $str);
    }

    /**
    * Prepends the Google redirector service to raw hyperlinks
    * Use this when the hyperlink is raw text, not transformed into a HTML tag
    *
    * Only works for the http protocol
    *
    * @param string $str
    * @return str
    */
    function redirectUrl($str) {
        return preg_replace("/http:\/\//i","http://www.google.com/url?sa=D&q=http://", $str);
    }
    /**
    * Removes slashes added by magic_quotes
    *
    * Borrowed from the Seagull project
    */
    function removeMagicQuotes(&$data) {
        static $magicQuotes;
        if (!isset($magicQuotes)) {
            $magicQuotes = get_magic_quotes_gpc();
        }
        if ($magicQuotes) {
            if (!is_array($data)) {
                $data = stripslashes($data);
            } else {
                array_walk($data, array('stringHandler', 'removeMagicQuotes'));
            }
        }
        return $data;
    }
    /**
     * Returns cleaned user input.
     *
     * Instead of addslashing potential ' and " chars, let's remove them and get
     * rid of any magic quoting which is enabled by default.  Also removes any
     * html tags and ASCII zeros
     *
     * @access  public
     * @param   mixed $var  Could be a string or an arry of strings
     * @return  string      $cleaned result.
     */
    function clean($var){
        if (isset($var)) {
            if (!is_array($var)) {
                $clean = strip_tags($var);
            } else {
                $clean = array_map(array('stringHandler', 'clean'), $var);
            }
        }
        return stringHandler::removeMagicQuotes(stringHandler::trimWhitespace($clean));
    }
    /**
    * Returns a string cleaned of script tags
    *
    * @access public
    * @param mixed $var Can be a string or an array of strings
    * @return string
    */
    function removeJs($var){
        if (isset($var)) {
            if (!is_array($var)) {
                $search = "/<script[^>]*?>.*?<\/script\s*>/i";
                $replace = '';
                $clean = preg_replace($search, $replace, $var);
            } else {
                $clean = array_map(array('stringHandler', 'removeJs'), $var);
            }
        }
        return stringHandler::trimWhitespace($clean);
    }

     /**
     * This removes whitespace at front and end of a string
     *
     * Borrowed from the Seagull project
     *
     * @param string $str Could be a fixed length string or an array of strings
     * @return mixed A string or array
     */
    function trimWhitespace($str){
        if (!is_array($str)) {
            $clean = trim($str);
        } else {
            $clean = array_map(array('stringHandler', 'trimWhitespace'), $str);
        }
        return $clean;
    }

}
?>