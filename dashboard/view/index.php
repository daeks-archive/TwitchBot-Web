<?php

	require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

  core::displayHeader();
  
  $query = explode('/', strtolower($_SERVER['QUERY_STRING']));
  if(sizeof($query) >= 1) {
    $stmt = $db->select('BOTS', "NAME='".strtolower($query[0])."'");
    if($stmt->rowCount() > 0) {
      $bot = $stmt->fetch();
      core::startExternalPage($bot);
      
      $db = new db();
      
      if($bot['BANNER'] != null && $bot['BANNER'] != '') {
        echo '<div style="background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url(\''.$bot['BANNER'].'\');" class="navbar-background hidden-xs hidden-sm display-md display-lg"></div>';
      } else {
        echo '<div class="navbar-background-template hidden-xs hidden-sm display-md display-lg"></div>';
      }
      
      echo '<div class="container hidden-xs hidden-sm display-md display-lg">';

      echo '<div class="row">';
      echo '<div class="col-md-8"><h1 class="text-white">'.$submodule->name.'</h1></div>';
      echo '<div class="col-md-4"><h1 class="text-white"></h1></div>';
      echo '</div>';

      echo '<div class="row">';
      echo '<div class="col-md-8">';
      
      echo '<div class="page-card page-card-margin">';
      echo '<div class="page-category-heading">';
      echo '<b>Welcome</b>';
      echo '</div>';
      echo '<p>Hello, my name is '.$bot['NAME'].'. Currently my dashboard only contains some interesing statistics about myself.</p>';
      echo '<p>I was born on '.date($bot['INSERTED']).' by my master '.$bot['INSERTBY'].'.</p>';
      echo '<p>My only purpose is to serve these users on '.APP.':</p>';
      echo '<ul>';
      foreach($db->select('CHANNELS', 'BOTID='.$bot['ID']. ' and enabled=1')->fetchAll() as $channel) {
        echo '<li><a href="'.APP.URL_SEPARATOR.ltrim($channel['NAME'], '#').'" target="_blank">'.$channel['NAME'].'</a></li>';
      }
      echo '</ul>';
      echo '</div>';
      
      echo '</div>';
      
      echo '<div class="col-md-4">';
 
      $total = $db->query("SELECT SUM(VALUE) as TOTAL FROM ARCHIVE WHERE BOTID=".$bot['ID']." and TYPE='TOTAL' and NAME='EVENTS'")->fetch();
      $time = $db->query("SELECT AVG(VALUE) as AVERAGE FROM ARCHIVE WHERE BOTID=".$bot['ID']." and TYPE='PLUGIN'")->fetch();
      $users = $db->query("SELECT SUM(VALUE) as TOTAL FROM ARCHIVE WHERE BOTID=".$bot['ID']." and TYPE='TOTAL' and NAME='USERS'")->fetch();
      $chatters = $db->query("SELECT SUM(VALUE) as TOTAL FROM ARCHIVE WHERE BOTID=".$bot['ID']." and TYPE='TOTAL' and NAME='CHATTERS'")->fetch();
      $write = $db->query("SELECT SUM(VALUE) as TOTAL FROM ARCHIVE WHERE BOTID=".$bot['ID']." and TYPE='WRITE' and NAME='PRIVMSG'")->fetch();
      $read = $db->query("SELECT SUM(VALUE) as TOTAL FROM ARCHIVE WHERE BOTID=".$bot['ID']." and TYPE='READ' and NAME='PRIVMSG'")->fetch();
      $whisper = $db->query("SELECT SUM(VALUE) as TOTAL FROM ARCHIVE WHERE BOTID=".$bot['ID']." and TYPE='READ' and NAME='WHISPER'")->fetch();
      $notice = $db->query("SELECT SUM(VALUE) as TOTAL FROM ARCHIVE WHERE BOTID=".$bot['ID']." and TYPE='READ' and NAME='NOTICE'")->fetch();
      $timeout = $db->query("SELECT SUM(VALUE) as TOTAL FROM ARCHIVE WHERE BOTID=".$bot['ID']." and TYPE='READ' and NAME='CLEARCHAT'")->fetch();
      
      $locale = $db->query("SELECT NAME, SUBNAME, VALUE FROM ARCHIVE WHERE BOTID=".$bot['ID']." and TYPE='TOTAL' and NAME='LOCALE'");
                  
      echo '<div class="page-card page-card-margin">';
      echo '<div class="page-category-heading">';
      echo '<b>Bot Statistics</b>';
      echo '</div>';
 
      echo '<p><div>Events: <div class="pull-right"><b>'.intval($total['TOTAL']).'</b></div></div>';
      if($total['TOTAL'] > 0) {
        echo '<div>Average Response Time: <div class="pull-right"><b>'.sprintf('%.3f', $time['AVERAGE']).'s</b></div></div></p>';
      }
      echo '<p><div>Channels: <div class="pull-right"><b>'.$db->select('CHANNELS', 'BOTID='.$bot['ID'].' and enabled=1')->rowCount().'</b></div></div>';
      echo '<div>Unique Users: <div class="pull-right"><b>'.intval($users['TOTAL']).'</b></div></div></p>';
      echo '<div>Send Messages: <div class="pull-right"><b>'.intval($write['TOTAL']).'</b></div></div>';
      echo '</div>';
      
      echo '<div class="page-card page-card-margin">';
      echo '<div class="page-category-heading">';
      echo '<b>Overall Statistics</b>';
      echo '</div>';
      echo '<p><div>Unique Chatters: <div class="pull-right"><b>'.intval($chatters['TOTAL']).'</b></div></div>';
      echo '<div>Timeouts: <div class="pull-right"><b>'.intval($timeout['TOTAL']).'</b></div></div></p>';
      echo '<p><div>Received Notices: <div class="pull-right"><b>'.intval($notice['TOTAL']).'</b></div></div>';
      echo '<div>Received Whispers: <div class="pull-right"><b>'.intval($whisper['TOTAL']).'</b></div></div></p>';
      echo '<div>Received Messages: <div class="pull-right"><b>'.intval($read['TOTAL']).'</b></div></div>';
      if($locale->rowCount() > 0 && $read['TOTAL'] > 0) {
        echo '<div>Language:</div>';
        echo '<ul>';
        $tmp = array();
        foreach($locale->fetchAll() as $row) {
        	if(!isset($tmp[$row['SUBNAME']])) {
        		$tmp[$row['SUBNAME']] = $row['VALUE'];
        	} else {
        		$tmp[$row['SUBNAME']] = $tmp[$row['SUBNAME']] + $row['VALUE'];
        	}
        }

        foreach($tmp as $key => $value) {
       	  $language = 'N/A';
       	  if($key != '') {
       	  	$language = $key;
       	  }
          echo '<li><div>'.strtoupper($language).' <div class="pull-right"><b>'.round($value/$read['TOTAL']*100, 2).'%</b></div></div></li>';
        }
        echo '</ul>';
      }
      echo '</div>';
      
      echo '<div class="page-card page-card-margin">';
      echo '<div class="page-category-heading">';
      echo '<b>Plugin Statistics</b>';
      echo '</div>';
      $plugintotal = $db->select('PLUGINS', 'BOTID='.$bot['ID']. ' and enabled=1')->rowCount();
      echo '<p><div>GLOBAL Plugins installed: <div class="pull-right"><b>'.$plugintotal.'</b></div></div></p>';
      echo '<ul>';
      
      $time = 0;      
      foreach($db->select('PLUGINS', 'BOTID='.$bot['ID']. ' and channelid=0 and enabled=1 order by name asc')->fetchAll() as $plugin) {
        $ptime = $db->query("SELECT AVG(VALUE) as AVERAGE FROM ARCHIVE WHERE BOTID=".$bot['ID']." and TYPE='PLUGIN' and name='".strtoupper($plugin['NAME'])."'")->fetch();
        echo '<li><div>'.$plugin['NAME'].' <div class="pull-right"><b>'.sprintf('%.3f', $ptime['AVERAGE']).'s</b></div></div></li>';
      }
      echo '</ul>';
      echo '</div>';
        
      echo '</div>';
      
      echo '</div>';
      echo '</div>';
    } else {
      echo '<br>';
      echo '<div class="hidden-xs hidden-sm display-md display-lg not-supported">';
      echo '<div class="alert alert-danger"><b>Bot does not exist</b><br>Please use a known bot.</div>';
      echo '</div>';
      die("");
    }
  } else {
    echo '<br>';
    echo '<div class="hidden-xs hidden-sm display-md display-lg not-supported">';
    echo '<div class="alert alert-danger"><b>Invalid Access</b><br>Please access this page with '.str_replace($_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']).'botname</div>';
    echo '</div>';
    die("");
  }

  core::endExternalPage();
	
?>