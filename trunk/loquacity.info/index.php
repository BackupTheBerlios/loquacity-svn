<?php
$baseURI = 'http://www.loquacity.info';
$templates = dirname(__FILE__) . '/templates';
$languages = dirname(__FILE__) . '/languages';

$ua = $_SERVER['HTTP_USER_AGENT'];
$accept_lang = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

// Right now, let's be "dumb" and only look at the first element of acceptible languages. This really violates the RFC
if(is_array($accept_lang)){
    $lang = array_shift($accept_lang);
    if(($pos = strstr($lang, '-')) !== false){
        $lang = substr($lang, 0, $pos);
    }
    
    if($lang == '' || $lang == '*'){ //default to english
        $lang = 'en';
    }
    if(file_exists($languages.'/'.$lang.'/') === false){
        $lang = 'en';
    }
}
else{
    $lang = 'en';
}

/*if ($cur_lang=$_POST['Languages'] == "") {
 $cur_lang="english";
 } else {
 $cur_lang=$_POST['Languages'];
 }
*/
include "{$templates}/header.inc";

include "{$languages}/{$lang}/index.inc";

include "{$templates}/footer.inc";

?>
