<?php
  
  require_once(dirname(realpath(__DIR__)).DIRECTORY_SEPARATOR.'config.php');
  	
	$db = new db();
	$module = utils::config();
	$submodule = utils::subconfig();
	
	$colors = array('Blue', 'Coral', 'DodgerBlue', 'SpringGreen', 'YellowGreen', 'Green', 'OrangeRed', 'Red', 'GoldenRod', 'HotPink', 'CadetBlue', 'SeaGreen', 'Chocolate', 'BlueViolet', 'Firebrick');
		
	if($module != null) {
	  config::includes(BASE.DIRECTORY_SEPARATOR.$module->id);
    define('CONTROL', URL_SEPARATOR.$module->id.URL_SEPARATOR.$submodule->id.URL_SEPARATOR.'c.php');
    define('DIALOG', URL_SEPARATOR.$module->id.URL_SEPARATOR.$submodule->id.URL_SEPARATOR.'d.php');
  }
		  	
?>