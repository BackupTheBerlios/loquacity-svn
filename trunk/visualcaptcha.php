<?php
/*
 * Created on May 23, 2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
/**
* TODO: make this process better
*
*/
include_once('bblog/config.php');
if(defined(C_CAPTCHA_ENABLE) && C_CAPTCHA_ENABLE == 'true'){
    require_once('bblog/libs/captcha/php-captcha.inc.php');
    
    $fonts = 'bblog/libs/captcha/fonts/';
    $aFonts = array($fonts.'VeraBd.ttf', $fonts.'VeraIt.ttf', $fonts.'Vera.ttf');
    
    $captcha = new PhpCaptcha($aFonts, 200, 60);
    
    //Set user options
    $captcha->SetWidth(C_CAPTCHA_WIDTH);
    $captcha->SetHeight(C_CAPTCHA_HEIGHT);
    $captcha->SetNumChars(C_CAPTCHA_CHARACTERS);
    $captcha->SetNumLines(C_CAPTCHA_LINES);
    if(C_CAPTCHA_ENABLE_SHADOWS === 'true'){
        $captcha->DisplayShadow(true);
    }
    if(C_CAPTCHA_OWNER_TEXT === 'true'){
        $captcha->SetOwnerText(BLOGURL);
    }
    $captcha->SetCharSet(C_CAPTCHA_CHARACTER_SET);
    if(C_CAPTCHA_CASE_INSENSITIVE === 'true'){
        $captcha->CaseInsensitive(true);
    }
    //$captcha->SetEdith(C_CAPTCHA_WIDTH);
    $captcha->SetMinFontSize(C_CAPTCHA_MIN_FONT);
    $captcha->SetMaxFontSize(C_CAPTCHA_MAX_FONT);
    if(C_CAPTCHA_USE_COLOR === 'true'){
        $captcha->UseColour(true);
    }
    $captcha->SetFileType(C_CAPTCHA_GRAPHIC_TYPE);
    $captcha->Create();
}
?>