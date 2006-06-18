<?php
/*
 * Loquacity Weblog http://www.loquacity.info/
 * Copyright (C) 2006 Kenneth Power <telcor@users.berlios.de>
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
function identify_admin_captcha(){
    return array (
        'name'           =>'captcha',
        'type'           =>'builtin',
        'nicename'       =>'Captcha Admin',
        'description'    =>'Configure and Enable Captcha use',
        'authors'         =>'TelCor',
        'licence'         =>'GPL'
    );
}

include_once('lib/manageCaptcha.class.php');
$bBlog->assign('form_type','edit');

$mc = new manageCaptcha($bBlog->_adb);
if(isset($_POST['captchado'])){
    save_configuration($mc);
}

$bBlog->assign('settings', current_configuration($mc));
$bBlog->display('captcha.html');

function current_configuration(&$mc){
    $config = $mc->getCurrentSettings();
    if($config){
        foreach($config as $row){
            if($row['name'] === 'CAPTCHA_ENABLE'){
                $config['enable_captcha'] = ($row['value'] == 'true') ? 'checked' : '';
            }
            if($row['name'] === 'CAPTCHA_ENABLE_SHADOWS'){
                $config['enable_shadows'] = ($row['value'] == 'true') ? 'checked' : '';
            }
            if($row['name'] === 'CAPTCHA_OWNER_TEXT'){
                $config['enable_owner'] = ($row['value'] == 'true') ? 'checked' : '';
            }
            if($row['name'] === 'CAPTCHA_CASE_INSENSITIVE'){
                $config['enable_case_insensitive'] = ($row['value'] == 'true') ? 'checked' : '';
            }
            if($row['name'] === 'CAPTCHA_USE_COLOR'){
                $config['enable_color'] = ($row['value'] == 'true') ? ' checked' : '';
            }
            else{
                $name = strtolower($row['name']);
                $config[$name] = str_replace('captcha_', 'enable', $row['value']);
            }
        }
    }
    return $config;
}
function save_configuration(&$mc){
    $curr['CAPTCHA_ENABLE'] = (isset($_POST['enable_captcha'])) ? 'true' : 'false';
    $curr['CAPTCHA_WIDTH'] = (isset($_POST['captcha_width'])) ? intval($_POST['captcha_width']): 200;
    $curr['CAPTCHA_HEIGHT'] = (isset($_POST['captcha_height'])) ? intval($_POST['captcha_height']): 50;
    $curr['CAPTCHA_CHARACTERS'] = (isset($_POST['captcha_characters'])) ? intval($_POST['captcha_characters']) : 5;
    $curr['CAPTCHA_LINES'] = (isset($_POST['captcha_lines'])) ? intval($_POST['captcha_lines']): 70;
    $curr['CAPTCHA_ENABLE_SHADOWS'] = (isset($_POST['captcha_enable_shadows'])) ? 'true': 'false';
    $curr['CAPTCHA_OWNER_TEXT'] = (isset($_POST['captcha_owner_text'])) ? 'true': 'false';
    $curr['CAPTCHA_CHARACTER_SET'] = (isset($_POST['captcha_character_set'])) ? StringHandling::clean($_POST['captcha_character_set']) : '';
    $curr['CAPTCHA_CASE_INSENSITIVE'] = (isset($_POST['captcha_case_insensitive'])) ? 'true': 'false';
    $curr['CAPTCHA_BACKGROUND'] = (isset($_POST['captcha_background'])) ? $_POST['captcha_background']: '';
    $curr['CAPTCHA_MIN_FONT'] = (isset($_POST['captcha_min_font'])) ? intval($_POST['captcha_min_font']): 16;
    $curr['CAPTCHA_MAX_FONT'] = (isset($_POST['captcha_max_font'])) ? intval($_POST['captcha_max_font']): 25;
    $curr['CAPTCHA_USE_COLOR'] = (isset($_POST['captcha_use_color'])) ? 'true': 'false';
    $curr['CAPTCHA_GRAPHIC_TYPE'] = (isset($_POST['captcha_graphic_type'])) ? $_POST['captcha_graphic_type']: 'jpg';
    $mc->saveConfiguration($curr);
}
?>