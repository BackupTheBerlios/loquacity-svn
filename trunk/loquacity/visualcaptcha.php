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
/* Loquacity - A weblogging application with simplicity in mind - http://www.loquacity.info
 * Copyright 2006 Kenneth Power <telcor@users.berlios.de>
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
include_once('bblog/config.php');
if(defined(C_CAPTCHA_ENABLE) && C_CAPTCHA_ENABLE == 'true'){
    include_once('bblog/3rdparty/captcha/php-captcha.inc.php');
    $fonts = 'bblog/3rdparty/captcha/fonts/';
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