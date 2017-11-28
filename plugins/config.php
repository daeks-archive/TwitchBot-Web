<?php
  
  require_once(dirname(realpath(__DIR__)).DIRECTORY_SEPARATOR.'config.php');
  utils::construct();
  	
	$db = new db();
	$module = utils::config();
	
	if($module != null) {
    define('CONTROL', URL_SEPARATOR.$module->id.URL_SEPARATOR.'c.php');
    define('DIALOG', URL_SEPARATOR.$module->id.URL_SEPARATOR.'d.php');
	  config::includes(BASE.DIRECTORY_SEPARATOR.$module->id);
  }
  
  define('CONFIG_DIALOG', '.dialog.php');
  define('CONFIG_CONTROL', '.control.php');
  	
?>