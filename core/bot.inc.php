<?php

	class bot {
	
    public static function checkConnection() {
      $so = @fsockopen(CLI_SERVER, CLI_PORT, $errno, $errstr, 5);
      if (!$so) {
         return json_decode('{"state" : "ERROR", "msg" : "NO_CONNECTION"}');
      } else {
        fputs($so, "quit\r\n");
         return json_decode('{"state" : "OK"}');
      }
    }
    
    public static function execute($cmd, $cache = CACHE) {
      if(!$cache || !file_exists(TMP.DIRECTORY_SEPARATOR.md5($cmd))) {
        $output = '';
        $so = @fsockopen(CLI_SERVER, CLI_PORT, $errno, $errstr, 5);
        if (!$so) {
          $output = '{"state" : "ERROR", "msg" : "NO_CONNECTION"}';
        } else {
          fputs($so, $cmd."\r\n");
          fputs($so, "quit\r\n");
          $lines = array();
          while (($buffer = fgets($so, 4096)) !== false) {
            array_push($lines, str_replace(array(chr(10), chr(13), chr(1)), '', $buffer));
          }
          $output = implode('', $lines);
        }
        if($cache) {
          file_put_contents(TMP.DIRECTORY_SEPARATOR.md5($cmd), $output);
        }
        return json_decode($output);
      } else {
        if (time()-filemtime(TMP.DIRECTORY_SEPARATOR.md5($cmd)) < TMP_LIFETIME) {
          return json_decode(file_get_contents(TMP.DIRECTORY_SEPARATOR.md5($cmd)));
        } else {
          unlink(TMP.DIRECTORY_SEPARATOR.md5($cmd));
          return self::execute($cmd);
        }
      }
    }		
	}

?>