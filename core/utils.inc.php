<?php
    
	class utils {
			
		public static $dateformat = 'd.m.Y - H:i:s';
		
		public static function construct() {
      if(defined('COOKIE_LIFETIME')) {
				ini_set('session.cookie_lifetime', COOKIE_LIFETIME);
			}

			//session_name(md5(BASE));
			session_start();
			
			if(defined('ANONYMOUS')) {
        //auth::reset();
      } else {
        auth::authenticate();
      }
		}		
				
		public static function config($module = null) {
      if($module == null) {
        if(isset($_SERVER['REQUEST_URI'])) {
          $location = explode(URL_SEPARATOR, rtrim(str_replace($_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']), '/?'));
          if(sizeof($location) > 1) {
            if (file_exists(BASE.DIRECTORY_SEPARATOR.$location[1].DIRECTORY_SEPARATOR.MODULE)) {
               return json_decode(file_get_contents(BASE.DIRECTORY_SEPARATOR.$location[1].DIRECTORY_SEPARATOR.MODULE));
            } else {
              return null;
            }
          } else {
            return null;
          }
        } else {
          return null;
        }
      } else {
        if (file_exists(BASE.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.MODULE)) {
             return json_decode(file_get_contents(BASE.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.MODULE));
          } else {
            return null;
          }
      }
    }
				
		public static function subconfig($module = null) {
      if($module == null) {
        if(isset($_SERVER['REQUEST_URI'])) {
          if (file_exists(BASE.substr(str_replace($_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']), 0, strrpos(str_replace($_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']), '/')).DIRECTORY_SEPARATOR.MODULE)) {
             return json_decode(file_get_contents(BASE.substr(str_replace($_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']), 0, strrpos(str_replace($_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']), '/')).DIRECTORY_SEPARATOR.MODULE));
          } else {
            return null;
          }
        } else {
          return null;
        }
      } else {
        if (file_exists(BASE.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.MODULE)) {
             return json_decode(file_get_contents(BASE.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.MODULE));
          } else {
            return null;
          }
      }
		}
		
		public static function strip($tmp) {
      $umlaute = Array('/ä/','/ö/','/ü/','/Ä/','/Ö/','/Ü/','/ß/', '/\'/');
      $replace = Array('ae','oe','ue','Ae','Oe','Ue','ss', '');
      
      return preg_replace($umlaute, $replace, $tmp);
		}
		
		public static function log($msg, $dump = '') {
      $db = new db();
      $db->exec("insert into log (message, dump, timestamp) values ('".$msg."','".$dump."', ".time().")");
      $db = null;
		}
		
	}
    
?>