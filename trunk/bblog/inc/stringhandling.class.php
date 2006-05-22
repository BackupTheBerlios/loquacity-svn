<?php
/**
* Class for handling string related functions
* @package bBlog
* @author Kenneth Power <kenneth.power@gmail.com> - last modified by $LastChangedBy: $
* @version $Id: $
* @copyright Kenneth Power <kenneth.power@gmail.com>
* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
* A pseudo static class, it never needs instantiated. This class
* serves to centralize various string handling functions, such as
* transforming typed hyperlinks into clickable links.
*
* @author Kenneth Power <kenneth.power@gmail.com>
* $LastModified$
* $Revision$
*/
class StringHandling{
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
        $str = ' '.$str;
        $et_pattern = '/\s<a([\s]+[^>]*)href[\s]*=[\s]*["\']([^"]+[.\s]*)(["\'][^>]*[\s]*)>([^<]+[.\s]*)<\/a>/i';
        $prot_pattern = '/\s([fh]+[t]{0,1}tp[s]*:\/\/([a-zA-Z0-9_~#=&%\/\:;@,\.\?\+-]+))/i';
        $simple_pattern = '/\s(www|ftp)\.([a-zA-Z0-9_~#=&%\/\:;@,\.\?\+-]+)/i';
        $patterns = array($et_pattern, $prot_pattern, $simple_pattern);
        
        $et_replace = ' <a$1href="http://www.google.com/url?sa=D&q=$2$3>$4</a>';
        $prot_replace = ' <a href="http://www.google.com/url?sa=D&q=$1">$2</a>';
        $simple_replace = ' <a href="http://www.google.com/url?sa=D&q=http://$0">$2</a>';
        $repl = array($et_replace, $prot_replace, $simple_replace);
        
        //Since the regex replace above adds a space within the HREF attribute, we need to remove it
        $str = preg_replace($patterns, $repl, $str);
        
        $str = str_replace('sa=D&q=http:// ', 'sa=D&q=http://', $str);
        
        //Return the result, removing the prepended space
        return substr($str, 1);
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
    * !This fixes double slashes
    * check to see if magic_quotes_gpc is set or not
    * and excape accordingly
    */
    function addslashes ($data) {
        return (get_magic_quotes_gpc()) ? $data : addslashes($data);
    }
}
?>