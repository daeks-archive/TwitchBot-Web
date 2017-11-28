<?php
  
  define('ANONYMOUS', true);
  require_once(dirname(realpath(__DIR__)).DIRECTORY_SEPARATOR.'config.php');
  	
	$db = new db();
	$module = utils::config();
	$submodule = utils::subconfig();
	
	if($module != null) {
	  config::includes(BASE.DIRECTORY_SEPARATOR.$module->id);
  }

  define('MAX', 50);
 		  	
?>