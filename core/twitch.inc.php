<?php

	class twitch {
	
    public static function token($data) {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, API.URL_SEPARATOR.'oauth2/token');
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch,CURLOPT_POST, 1);
      curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
      $output = curl_exec($ch);
      curl_close($ch);
      return json_decode($output);
    }
			
		public static function validate($token) {
      if (extension_loaded('curl')) {
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, API.URL_SEPARATOR);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $headers = array();
        $headers[] = "Accept: application/vnd.twitchtv.v3+json";
        $headers[] = "Authorization: OAuth ".$token."";
        $headers[] = "Client-ID: ".CLIENTID."";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output);
      } else {
        die("No CURL loaded");
      }
		}
		
		public static function select($token, $query, $params = null) {
      if (extension_loaded('curl')) {
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, API.URL_SEPARATOR.$query);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $headers = array();
        $headers[] = "Accept: application/vnd.twitchtv.v3+json";
        if($token != null) {
          $headers[] = "Authorization: OAuth ".$token."";
        }
        $headers[] = "Client-ID: ".CLIENTID."";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output);
      } else {
        die("No CURL loaded");
      }
		}
		
	}

?>