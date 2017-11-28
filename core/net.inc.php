<?php

	class net {
	
    public static $agent = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.115 Safari/537.36';
	
		public static function success($data = null, $event = null) {
			self::ajax(200,$data,$event);
		}

    public static function noaccess($data = null, $event = null) {
			self::ajax(401,$data,$event);
		}
				
		public static function error($data, $event = null) {
			self::ajax(500,$data,$event);
		}
		
		public static function fatal($data, $event = null) {
			self::ajax(999,$data,$event);
		}
		
		public static function ajax($status, $data, $event) {
      echo '{ "status" : '.$status.', "data" : "'.(($data == null) ? '' : htmlentities($data)).'", "event" : "'.(($event == null) ? '' : $event).'" }';
		}
		
		public static function data($total, $data) {
      echo '{"status":200,"data":'.(($data == null) ? '' : $data).',"total":'.$total.'}';
		}
		
		public static function addCookie($key, $val, $time = COOKIE_LIFETIME) {
			setcookie ($key, $val, time() + $time, URL_SEPARATOR);
		}
		
		public static function removeCookie($key) {
			setcookie ($key, '', -1);
		}
		
		public static function getCookie($key) {
			if(isset($_COOKIE[$key])) {
				return $_COOKIE[$key];
			} else {
				return '';
			}
		}
		
		public static function get($key) {
      if(isset($_GET[$key])) {
        return str_replace('undefined', '', $_GET[$key]);
      } else {
        return '';
      }		
		}
		
		public static function post($key) {
      if(isset($_POST[$key])) {
        return str_replace('undefined', '', $_POST[$key]);
      } else {
        return '';
      }		
		}
		
		public static function ping($url) {
      $nurl = parse_url($url);
      $socket = @fsockopen($nurl['host'], (isset($nurl['port'])? $nurl['port'] : 80), $errno, $errstr, 5);
      if(!$socket) {
          return $errno."@".$errstr;
      } else {
          fclose($socket);
          return "OK";
      } 
    }
    
    public static function size($url) {
      return (isset(get_headers($url, 1)['Content-Length']) ? get_headers($url, 1)['Content-Length'] : 0);
    }
		
		public static function content($url, $getstartbytes = false) {
      if (extension_loaded('curl')) {
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url);
        if($getstartbytes) {
          curl_setopt($curl, CURLOPT_HTTPHEADER, array('Range: bytes=0-32768'));
        }
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, self::$agent); 
        return curl_exec($ch); 
      } else {
        return file_get_contents($url); 
      }
		}
		
		public static function browse($host, $url) {
      if (extension_loaded('curl')) {
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, 'http://'.$host.$url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, self::$agent); 
        return curl_exec($ch);
      } else {
        $client = new HttpClient($host);
        $client->setDebug(false);
        $client->get($url);
        return $client->getContent(); 
      }
		}
		
	}

?>