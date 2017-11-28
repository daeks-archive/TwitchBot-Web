<?php

	require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

  core::displayHeader();
  core::startPage();
  core::startModule();
   
  echo '<br><div class="row">';
  echo '<div class="col-md-8">';
  
  $stmt = $db->select('BOTS', "ID=".auth::getBotId());
  $bot = $stmt->fetch();
  
  echo '<div class="panel panel-primary">';
  echo '<div class="panel-heading"><h3 class="panel-title">Welcome</h3></div>';
  echo '<div class="panel-body">';
  echo '<p>Hello, my name is '.$bot['NAME'].'. Currently my dashboard only contains some interesing statistics about myself.</p>';
  echo '<p>I was born on '.date($bot['INSERTED']).' by my master '.$bot['INSERTBY'].'.</p>';
  echo '<p>My only purpose is to serve these users on '.APP.':</p>';
  echo '<ul>';
  foreach($db->select('CHANNELS', 'BOTID='.auth::getBotId(). ' and enabled=1')->fetchAll() as $channel) {
    echo '<li><a href="'.APP.URL_SEPARATOR.ltrim($channel['NAME'], '#').'" target="_blank">'.$channel['NAME'].'</a></li>';
  }
  echo '</ul>';
  echo '</div>';
  echo '</div>';
 
  echo '</div>';
  
  echo '<div class="col-md-4">';
  
  $topcmd = $db->query("SELECT NAME, MAX(VALUE) as TOTAL FROM PLUGIN_STATS WHERE BOTID=".auth::getBotId()." and CHANNELID=".auth::getChannelId()." limit 1")->fetch();
  echo '<div class="panel panel-info">';
  echo '<div class="panel-heading"><h3 class="panel-title">Top Command</h3></div>';
  echo '<div class="panel-body">';
  if(isset($topcmd['NAME'])) {
    echo '<h4><b>'.$topcmd['NAME'].'</b></h4> with '.$topcmd['TOTAL'].' hits';
  } else {
    echo 'No Top command found';
  }
  echo '</div>';
  echo '</div>';
  
  $total = $db->query("SELECT SUM(VALUE) as TOTAL FROM ARCHIVE WHERE BOTID=".auth::getBotId()." and CHANNELID=".auth::getChannelId()." and TYPE='TOTAL' and NAME='EVENTS'")->fetch();
  $time = $db->query("SELECT AVG(VALUE) as AVERAGE FROM ARCHIVE WHERE BOTID=".auth::getBotId()." and CHANNELID=".auth::getChannelId()." and TYPE='PLUGIN'")->fetch();
  $users = $db->query("SELECT SUM(VALUE) as TOTAL FROM ARCHIVE WHERE BOTID=".auth::getBotId()." and CHANNELID=".auth::getChannelId()." and TYPE='TOTAL' and NAME='USERS'")->fetch();
  $chatters = $db->query("SELECT SUM(VALUE) as TOTAL FROM ARCHIVE WHERE BOTID=".auth::getBotId()." and CHANNELID=".auth::getChannelId()." and TYPE='TOTAL' and NAME='CHATTERS'")->fetch();
  $write = $db->query("SELECT SUM(VALUE) as TOTAL FROM ARCHIVE WHERE BOTID=".auth::getBotId()." and CHANNELID=".auth::getChannelId()." and TYPE='WRITE' and NAME='PRIVMSG'")->fetch();
  $read = $db->query("SELECT SUM(VALUE) as TOTAL FROM ARCHIVE WHERE BOTID=".auth::getBotId()." and CHANNELID=".auth::getChannelId()." and TYPE='READ' and NAME='PRIVMSG'")->fetch();
  $whisper = $db->query("SELECT SUM(VALUE) as TOTAL FROM ARCHIVE WHERE BOTID=".auth::getBotId()." and CHANNELID=".auth::getChannelId()." and TYPE='READ' and NAME='WHISPER'")->fetch();
  $notice = $db->query("SELECT SUM(VALUE) as TOTAL FROM ARCHIVE WHERE BOTID=".auth::getBotId()." and CHANNELID=".auth::getChannelId()." and TYPE='READ' and NAME='NOTICE'")->fetch();
  $timeout = $db->query("SELECT SUM(VALUE) as TOTAL FROM ARCHIVE WHERE BOTID=".auth::getBotId()." and CHANNELID=".auth::getChannelId()." and TYPE='READ' and NAME='CLEARCHAT'")->fetch();
  
  $locale = $db->query("SELECT NAME, SUBNAME, VALUE FROM ARCHIVE WHERE BOTID=".$bot['ID']." and CHANNELID=".auth::getChannelId()." and TYPE='TOTAL' and NAME='LOCALE'");
  
  echo '<div class="panel panel-default">';
  echo '<div class="panel-heading"><h3 class="panel-title">Bot Statistics</h3></div>';
  echo '<div class="panel-body">';
  echo '<p><div>Events: <div class="pull-right"><b>'.intval($total['TOTAL']).'</b></div></div>';
  echo '<p><div>Channels: <div class="pull-right"><b>'.$db->select('CHANNELS', 'BOTID='.auth::getBotId().' and enabled=1')->rowCount().'</b></div></div>';
  echo '<div>Unique Users: <div class="pull-right"><b>'.intval($users['TOTAL']).'</b></div></div></p>';
  echo '<div>Send Messages: <div class="pull-right"><b>'.intval($write['TOTAL']).'</b></div></div>';
  echo '</div>';
  echo '</div>';
  
  echo '<div class="panel panel-default">';
  echo '<div class="panel-heading"><h3 class="panel-title">Channel Statistics</h3></div>';
  echo '<div class="panel-body">';
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
  echo '</div>';
  
  /*echo '<div class="panel panel-info">';
  echo '<div class="panel-heading"><h3 class="panel-title">Top Chatter</h3></div>';
  echo '<div class="panel-body">';
  echo 'Under Construction';
  echo '</div>';
  echo '</div>';*/
    
  echo '</div>';
  echo '</div>';

  core::endModule();
  core::endPage();
	
?>