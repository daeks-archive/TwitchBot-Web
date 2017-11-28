<?php

	require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

  core::displayHeader();
  
  $query = explode('/', strtolower($_SERVER['QUERY_STRING']));
  if(sizeof($query) >= 1) {
    $stmt = $db->select('BOTS', "NAME='".strtolower($query[0])."'");
    if($stmt->rowCount() > 0) {
      $bot = $stmt->fetch();
      if(sizeof($query) == 2) {
        $stmt2 = $db->select('CHANNELS', 'BOTID='.$bot['ID']." and NAME='#".strtolower($query[1])."'");
        if($stmt2->rowCount() > 0) {
          $channel = $stmt2->fetch();      
          
          $stmt3 = $db->select('PLUGINS', "BOTID=".$bot['ID']." and (CHANNELID=".$channel['ID']." or CHANNELID=0) and NAME='".$module->id."' and enabled=1 order by botid desc, channelid desc");
          if($stmt3->rowCount() > 0) {
            $plugin = $stmt3->fetch();
            core::startExternalPage($bot, $channel);
          
            if($channel['BANNER'] != null && $channel['BANNER'] != '') {
              echo '<div style="background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url(\''.$channel['BANNER'].'\');" class="navbar-background hidden-xs hidden-sm display-md display-lg"></div>';
            } else {
              echo '<div class="navbar-background-template hidden-xs hidden-sm display-md display-lg"></div>';
            }
            
            echo '<div class="container hidden-xs hidden-sm display-md display-lg">';
            
            echo '<div class="row">';
            echo '<div class="col-md-8"><h1 class="text-white">'.strtoupper(ltrim($channel['NAME'],'#')).' - '.$submodule->name.'</h1></div>';
            echo '<div class="col-md-4"><h1 class="text-white">FAQ</h1></div>';
            echo '</div>';

            echo '<div class="row">';
            echo '<div class="col-md-8 page-card">';
            
            
            $stmt4 = $db->query('SELECT * FROM plugin_commands WHERE BOTID='.$bot['ID'].' and CHANNELID='.$channel['ID'].' order by NAME asc');
            if($stmt4->rowCount() > 0) {
              $i = 1;
              foreach($stmt4->fetchAll() as $command) {        
                echo '<div class="card">';
                echo '<div style="flex-basis: 150px;" class="card-block card-ranking-block">';
                echo '<h6 class="text-muted">Command</h6><h5>'.$command['NAME'].'</h5>';
                echo '</div>';
                echo '<div style="flex-basis: 800px;" class="card-block">';
                echo '<div class="card-user">';
                echo '<div class="card-user-name">';
                echo ''.$command['VALUE'].'';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                $i++;
              }
            } else {
              echo '<div style="flex-basis: 700px;" class="card-block">';
              echo '<h5>No data available.</h5>';
              echo '</div>';
            }
            
            echo '</div>';
            
            echo '<div class="col-md-4">';
            
            echo '<div class="page-card page-card-margin">';
            echo '<div class="page-category-heading">';
            echo '<b>What is this?</b>';
            echo '</div>';
            echo '<p>Commands are custom messages to help users on the channel.</p>';
            echo '</div>';
              
            echo '</div>';
            
            echo '</div>';
            echo '</div>';
          } else {
            core::startExternalPage($bot, $channel);
            echo '<div class="container">';
            echo '<div class="hidden-xs hidden-sm display-md display-lg not-supported">';
            echo '<div class="alert alert-danger"><b>Plugin disabled</b><br>This plugin is not enabled for this channel';
            echo '</div>';
            echo '</div>';
          }
        } else {
          core::startExternalPage($bot);
          echo '<div class="container">';
          echo '<div class="hidden-xs hidden-sm display-md display-lg not-supported">';
          echo '<div class="alert alert-danger"><b>Channel does not exist on '.$bot['NAME'].'</b><br>Please use a known channel.';        
          $stmt2 = $db->select('CHANNELS', 'BOTID='.$bot['ID']);
          echo '<ul>';
          foreach($stmt2->fetchAll() as $channel) {
            echo '<li><a href="'.str_replace(basename(__FILE__), '', $_SERVER['SCRIPT_NAME']).'?'.$bot['NAME'].URL_SEPARATOR.strtolower(ltrim($channel['NAME'],'#')).'" target="_self">'.$channel['NAME'].'</a></li>';
          } 
          echo '</ul>';
          echo '</div>';
          echo '</div>';
          echo '</div>';
        }
      } else {
        core::startExternalPage($bot);
        echo '<div class="container">';
        echo '<div class="hidden-xs hidden-sm display-md display-lg not-supported">';
        echo '<div class="alert alert-danger"><b>Invalid Access</b><br>Please access this page with '.str_replace($_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']).'botname/channel';
        $stmt2 = $db->select('CHANNELS', 'BOTID='.$bot['ID']);
        echo '<ul>';
        foreach($stmt2->fetchAll() as $channel) {
          echo '<li><a href="'.str_replace(basename(__FILE__), '', $_SERVER['SCRIPT_NAME']).'?'.$bot['NAME'].URL_SEPARATOR.strtolower(ltrim($channel['NAME'],'#')).'" target="_self">'.$channel['NAME'].'</a></li>';
        } 
        echo '</ul>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
      }
    } else {
      echo '<br>';
      echo '<div class="container">';
      echo '<div class="hidden-xs hidden-sm display-md display-lg not-supported">';
      echo '<div class="alert alert-danger"><b>Bot does not exist</b><br>Please use a known bot.</div>';
      echo '</div>';
      echo '</div>';
    }
  } else {
    echo '<br>';
    echo '<div class="hidden-xs hidden-sm display-md display-lg not-supported">';
    echo '<div class="alert alert-danger"><b>Invalid Access</b><br>Please access this page with '.str_replace($_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']).'botname/channel</div>';
    echo '</div>';
  }

  core::endExternalPage();
	
?>