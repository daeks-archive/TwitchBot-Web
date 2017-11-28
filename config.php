<?php

	define('BASE', (($_SERVER['DOCUMENT_ROOT'] != '') ? $_SERVER['DOCUMENT_ROOT'] : dirname(realpath(__FILE__))));
	define('NAME', 'Twitch D-Network');
	define('FAVICON', 'favicon.ico');
	define('BRAND', 'core/img/brand.jpg');
	define('COOKIE_LIFETIME', 60*60*24*7*4*3);
	
	define('TMP', BASE.DIRECTORY_SEPARATOR.'cache');
	define('TMP_LIFETIME', 300);
	
	define('INC', BASE.DIRECTORY_SEPARATOR.'core');
  define('JS', BASE.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'js');
  define('CSS', BASE.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'css');

	define('DB_HOST', 'localhost');
	define('DB_NAME', '');
  define('DB_SRV', 'mysql:host='.DB_HOST.';dbname='.DB_NAME);
	define('DB_USER', '');
	define('DB_PWD', '');
	
	define('URL_SEPARATOR', '/');
	
	define('CACHE' , false);
	define('COMPRESS' , true);
	
	define('CLI_BASE', 'C:\twitchbot-console');
  define('CLI_SERVER', 'localhost');
  define('CLI_PORT', 6667);
	
	define('APP', 'https://twitch.tv');
	
	define('API', 'https://api.twitch.tv/kraken');
  define('CLIENTID', '');
  define('SECRET', '');
  define('REDIRECTURL', '');
  
	define('AUTH', '/auth');
  define('AUTH_FORCE', true);
  
	define('START', 'dashboard');
	define('MODULE', 'config.json');
  define('PLUGIN', 'plugins.json');

	config::includes(INC);
	
	class config {
	
		public static function includes($path) {
			foreach (scandir($path) as $include){
				if(is_file($path.DIRECTORY_SEPARATOR.$include) && strpos($path.DIRECTORY_SEPARATOR.$include, '.inc.') !== false && strtoupper(pathinfo($include, PATHINFO_EXTENSION)) == 'PHP'){
					require_once($path.DIRECTORY_SEPARATOR.$include);
				}
			}
		}
		
		public static function libraries($path) {
			foreach (scandir($path) as $include){
				if(is_file($path.DIRECTORY_SEPARATOR.$include) && strpos($path.DIRECTORY_SEPARATOR.$include, '.lib.') !== false && strtoupper(pathinfo($include, PATHINFO_EXTENSION)) == 'PHP'){
					require_once($path.DIRECTORY_SEPARATOR.$include);
				}
			}
		}
	
	}

?>