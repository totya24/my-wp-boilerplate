<?php

if(isset($_GET['debug'])){
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
}

$currentTheme = wp_get_theme();

define('THEME_TEXTDOMAIN', $currentTheme->get('TextDomain'));

$themeOptions = array(
    'textdomain' => THEME_TEXTDOMAIN,
    'usePiklist' => true,
    'twig' => array(
        'debug' => false,
        'paths' => array(
            'svg' => get_template_directory() . '/assets/svg'
        )
    ),
    'adminAssets' => array(
        'css' => false,
        'js' => false,
        'editorStyle' => false
    ),
    'disableJquery' => true,
    'addScriptJs' => true,
);

require_once('core/core.php');

$tweaks = glob(get_template_directory()."/tweaks/*.php");
if(is_array($tweaks)){
    foreach($tweaks as $tweak){
        require_once($tweak);
    }
}

$includes = glob(get_template_directory()."/includes/*.php");
if(is_array($includes)){
    foreach($includes as $include){
        require_once($include);
    }
}