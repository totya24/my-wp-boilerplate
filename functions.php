<?php

if(isset($_GET['debug'])){
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
}

$currentTheme = wp_get_theme();

define('THEME_TEXTDOMAIN', $currentTheme->get('TextDomain'));

$themeOptions = array(
	'textdomain' => THEME_TEXTDOMAIN,
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
	'disableXMLRPC' => true, 
	'disableEmojis' => true, 
	'disableJquery' => true,
	'addScriptJs' => true,
	'disableComments' => true,
	'disableFeeds' => true,
	'disableRestApi' => true,
	'disableEmbed' => true,
	'slowHeartBeat' => true,
	'hidePosts' => false
);

require_once('core/core.php');

$includes = glob(get_template_directory()."/includes/*.php");
if(is_array($includes)){
	foreach($includes as $include){
		require_once($include);
	}
}