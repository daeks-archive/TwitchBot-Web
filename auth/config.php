<?php
  
  define('ANONYMOUS', true);
  require_once(dirname(realpath(__DIR__)).DIRECTORY_SEPARATOR.'config.php');
  utils::construct();
  
	$db = new db();
	$module = json_decode('{ "id":"auth", "name":"Authentification" }');
	
	if($module != null) {
	  config::includes(BASE.DIRECTORY_SEPARATOR.$module->id);
  }

  define('AUTH_SCOPES', 'chat_login+user_read+channel_subscriptions+channel_check_subscription+channel_editor+channel_feed_read+channel_feed_edit+channel_commercial');
	  	
?>