<?php

  require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');
  
  if(auth::hasAccess()) {
    $query = explode('/', strtolower($_SERVER['QUERY_STRING']));
    if(sizeof($query) >= 1) {
      $stmt = $db->select('BOTS', "NAME='".strtolower($query[0])."'");
      if($stmt->rowCount() > 0) {
        $bot = $stmt->fetch();
        if(sizeof($query) == 3) {
          $stmt2 = $db->select('CHANNELS', 'BOTID='.$bot['ID']." and NAME='#".strtolower($query[1])."'");
          if($stmt2->rowCount() > 0) {
            $channel = $stmt2->fetch();
            $stmt = $db->query('SELECT *, FIND_IN_SET( experience, (SELECT GROUP_CONCAT( experience ORDER BY experience DESC ) FROM plugin_ranks WHERE BOTID='.$bot['ID'].' and CHANNELID='.$channel['ID'].' and enabled=1)) AS RANK FROM plugin_ranks WHERE BOTID='.$bot['ID'].' and CHANNELID='.$channel['ID'].' and enabled=1 and UPPER(NAME) like \'%'.$query[2].'%\' order by level DESC, EXPERIENCE DESC limit 0, '.MAX);
            if($stmt->rowCount() > 0) {
              foreach($stmt->fetchAll() as $user) {        
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
          } else {
            echo '<div style="flex-basis: 700px;" class="card-block">';
            echo '<h5>Channel does not exist.</h5>';
            echo '</div>';
          }
        } else {
          echo '<div style="flex-basis: 700px;" class="card-block">';
          echo '<h5>No Search data available.</h5>';
          echo '</div>';
        }
      } else {
        echo '<div style="flex-basis: 700px;" class="card-block">';
        echo '<h5>Bot does not exist.</h5>';
        echo '</div>';
      }
    } else {
      echo '<div style="flex-basis: 700px;" class="card-block">';
      echo '<h5>Invalid Request.</h5>';
      echo '</div>';
    }
  } else {
    die(net::noaccess('No Access'));
  }
  
?>