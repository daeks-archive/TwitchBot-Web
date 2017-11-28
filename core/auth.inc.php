<?php
    
	class auth {
	
    public static $owner = 'owner';
				
		public static function authenticate() {
			if(!isset($_SERVER['SERVER_NAME'])) {
			  die('');
      } else {
        if(!auth::hasAccess()) {
          header('Location: '.AUTH);
          die("");
        }
			}
		}
	
		public static function logout() {
			session_unset();
			header('Location: '.$_SERVER['PHP_SELF']);
			die("");
		}
		
		public static function hasAccess() {
			return (isset($_SESSION['AUTH']) ? true : false);
		}
		
		public static function checkAccess($level = '') {
      if(isset($_SESSION['USERLEVEL'])) {
        foreach(explode(' ', $_SESSION['USERLEVEL']) as $userlevel) {
          if(strtolower($userlevel) == strtolower($level)) {
            return true;
          }
        }
      }
			return false;
		}
		
		public static function getUserName() {
			return (isset($_SESSION['USERNAME']) ? $_SESSION['USERNAME'] : null);
		}
		
		public static function getUserId() {
			return (isset($_SESSION['USERID']) ? $_SESSION['USERID'] : null);
		}
		
		public static function setBotId($botid) {
			$_SESSION['BOTID'] = $botid;
		}
		
		public static function getBotId() {
			return (isset($_SESSION['BOTID']) ? $_SESSION['BOTID'] : 0);
		}
		
		public static function setChannelId($channelid) {
			$_SESSION['CHANNELID'] = $channelid;
		}
		
		public static function getChannelId() {
			return (isset($_SESSION['CHANNELID']) ? $_SESSION['CHANNELID'] : 0);
		}
		
		public static function login($username, $userid, $botid, $channelid, $level = '') {
      $_SESSION['USERID'] = $userid;
      $_SESSION['BOTID'] = $botid;
      $_SESSION['CHANNELID'] = $channelid;
      $_SESSION['USERNAME'] = strtolower($username);
      $_SESSION['USERLEVEL'] = strtolower($level);
      $_SESSION['AUTH'] = time();
		}
		
		public static function reset() {
      net::removeCookie('USERNAME');
      net::removeCookie('USERLOGO');
      net::removeCookie('USERHASH');
      unset($_SESSION['BOTID']);
      unset($_SESSION['CHANNELID']);
      unset($_SESSION['USERID']);
      unset($_SESSION['USERNAME']);
      unset($_SESSION['USERLEVEL']);
      unset($_SESSION['AUTH']);
		}
	}
    
?>