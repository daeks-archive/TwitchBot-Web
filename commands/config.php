<?php
  
  require_once(dirname(realpath(__DIR__)).DIRECTORY_SEPARATOR.'config.php');
  utils::construct();
  	
	$db = new db();
	$module = utils::config();
	
	if($module != null) {
	  config::includes(BASE.DIRECTORY_SEPARATOR.$module->id);
  }
		  	
?>