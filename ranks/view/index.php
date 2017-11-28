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
          core::startExternalPage($bot, $channel);
            
          $stmt3 = $db->select('PLUGINS', "BOTID=".$bot['ID']." and (CHANNELID=".$channel['ID']." or CHANNELID=0) and NAME='".$module->id."' and enabled=1  order by botid desc, channelid desc");
          if($stmt3->rowCount() > 0) {
            $plugin = $stmt3->fetch();
                  
            if($channel['BANNER'] != null && $channel['BANNER'] != '') {
              echo '<div style="background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url(\''.$channel['BANNER'].'\');" class="navbar-background hidden-xs hidden-sm display-md display-lg"></div>';
            } else {
              echo '<div class="navbar-background-template hidden-xs hidden-sm display-md display-lg"></div>';
            }
            
            echo '<div class="container hidden-xs hidden-sm display-md display-lg">';
            
            echo '<div class="row">';
            echo '<div class="col-md-8"><h1 class="text-white">'.strtoupper(ltrim($channel['NAME'],'#')).' - '.$submodule->name.' Top '.MAX.'</h1></div>';
            echo '<div class="col-md-4"><h1 class="text-white">FAQ</h1></div>';
            echo '</div>';

            echo '<div class="row">';
            echo '<div class="col-md-8 page-card" id="list">';
            
            
            $stmt4 = $db->query('SELECT *, FIND_IN_SET( experience, (SELECT GROUP_CONCAT( experience ORDER BY experience DESC ) FROM plugin_ranks WHERE BOTID='.$bot['ID'].' and CHANNELID='.$channel['ID'].' and enabled=1)) AS RANK FROM plugin_ranks WHERE BOTID='.$bot['ID'].' and CHANNELID='.$channel['ID'].' and enabled=1 order by level DESC, EXPERIENCE DESC limit 0, '.MAX);
            if($stmt4->rowCount() > 0) {
              foreach($stmt4->fetchAll() as $user) {        
                echo '<div class="card card-ranking">';
                echo '<div style="flex-basis: 70px;" class="card-block card-ranking-block">';
                echo '<h6 class="text-muted">Rank</h6><h3>#'.$user['RANK'].'</h3>';
                echo '</div>';
                echo '<div style="flex-basis: 300px;" class="card-block">';
                echo '<div class="card-user">';
                echo '<div class="card-user-avatar">';
                $stmt5 = $db->select('USERS', "NAME='".strtolower($user['NAME'])."'");
                if($stmt5->rowCount() > 0) {
                  $account = $stmt5->fetch();
                  if($account['LOGO'] != null) {
                    echo '<a href="#"><img src="'.$account['LOGO'].'" width="32" height="32"></a>';
                  } else {
                    echo '<a href="#"><img src="../../core/img/avatar.png" width="32" height="32"></a>';
                  }
                } else {
                  echo '<a href="#"><img src="../../core/img/avatar.png" width="32" height="32"></a>';
                }
                echo '</div>';
                echo '<div class="card-user-name">';
                echo '<h4><a href="'.APP.URL_SEPARATOR.$user['NAME'].'" target="_blank">'.$user['NAME'].'</a></h4>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div style="flex-basis: 150px;" class="card-block align-right">';
                echo '<h6 class="text-muted">Experience</h6><h3>'.$user['EXPERIENCE'].'</h3>';
                echo '</div>';
                echo '<div style="flex-basis: 50px;" class="card-block align-right">';
                echo '<h6 class="text-muted">Level</h6><h3><span style="font-weight: 300;">';
                
                $levelcolor = 'white';
                if($user['LEVEL'] > 0 && $user['LEVEL'] <= 10) {
                  $levelcolor = '#777';
                } else if($user['LEVEL'] > 10 && $user['LEVEL'] <= 20) {
                  $levelcolor = '#d2d500';
                } else if($user['LEVEL'] > 20 && $user['LEVEL'] <= 30) {
                  $levelcolor = '#b3ee00';
                } else if($user['LEVEL'] > 30 && $user['LEVEL'] <= 40) {
                  $levelcolor = '#ff9600';
                } else if($user['LEVEL'] > 40 && $user['LEVEL'] <= 50) {
                  $levelcolor = '#ff0000';
                } else if($user['LEVEL'] > 50 && $user['LEVEL'] <= 60) {
                  $levelcolor = '#00ffff';
                } else if($user['LEVEL'] > 60 && $user['LEVEL'] <= 70) {
                  $levelcolor = '#009fff';
                } else if($user['LEVEL'] > 70 && $user['LEVEL'] <= 80) {
                  $levelcolor = '#7a62d3';
                } else if($user['LEVEL'] > 80 && $user['LEVEL'] <= 90) {
                  $levelcolor = '#fc00ff';
                } else if($user['LEVEL'] > 90 && $user['LEVEL'] <= 100) {
                  $levelcolor = '#7700a9';
                } else if($user['LEVEL'] > 100) {
                  $levelcolor = '#00a938';
                }
                
                echo '<div style="border-color: '.$levelcolor.';" class="level mini-level"><span class="mini-level-text">'.$user['LEVEL'].'</span></div>';
                echo '</span></h3>';
                echo '</div>';
                echo '<div style="flex-basis: 70px;" class="card-block align-right">';
                
                $current = (((($user['LEVEL'] * 20) * $user['LEVEL'] * 0.8) + $user['LEVEL'] * 100) - 16);
                $next = ((((($user['LEVEL']+1) * 20) * ($user['LEVEL']+1) * 0.8) + ($user['LEVEL']+1) * 100) - 16);
                
                echo '<h6 class="text-muted">Progress</h6><h3>'.floor((($user['EXPERIENCE'] - $current-1)/($next-$current))*100).'%</h3>';
                echo '</div>';
                echo '</div>';  
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
            echo '<b>How does it work?</b>';
            echo '</div>';
            echo '<p>Levels are a simple representation of your activity.</p>';
            echo '<b>Description</b>';
            echo '<ul>';
            echo '<li>Sending a message increases your activity meter by <b class="text-success">1</b>.</li>';
            echo '<li>Every 5 minutes, your activity meter is reduced by <b class="text-success">5</b>.</li>';
            echo '<li>Every 5 minutes, you get <b class="text-danger">16 or 20 XP</b> if your activity meter is at least at <b class="text-success">1</b>.</li>';
            echo '</ul>';
            echo '</div>';
            echo '<div class="page-card page-card-margin">';
            echo '<div class="page-category-heading">';
            echo '<b>Legend</b>';
            echo '</div>';
            echo '<p>You require a specific amount of experience to gain higher levels.</p>';
            echo '<p><table class="level-table"><tr><th>Level</th><th>Experience</th></tr>';
            for($i=150;$i >= 1;$i--) {
              if($i%5 == 0) {
                echo '<tr><td>'.$i.'</td><td>'.(((($i * 20) * $i * 0.8) + $i * 100) - 16).'</td></tr>';
              }
            }
            echo '</table></p>';
            echo '</div>';
            
            echo '</div>';
              
            echo '</div>';
            echo '</div>';
          } else {
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
    echo '<div class="alert alert-danger"><b>Invalid Access</b><br>Please access this page with '.str_replace($_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']).'botname</div>';
    echo '</div>';
  }

  core::endExternalPage();
	
?>