<?php

  require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');
   
  if(auth::hasAccess()) {
    $output = '';
    foreach($_GET as $key=>$value) {
      $output .= $key.'='.$value.';';
    }
  
    if(net::get('a') != '') {
      switch (net::get('a')) {
        case 'enable':
          $stmt= $db->select('PLUGIN_TIMERS', 'ID = '.net::get('i'));
          if($stmt->rowCount() > 0) {
            $timer = $stmt->fetch();
            core::startModal('Enable '.$timer['NAME'], CONTROL.'?a=enable&i='.net::get('i'));
            echo 'Do you really want to enable this timer?';
            core::endModal('Enable', 'success');
          } else {
            echo 'Not Found'.$output;
          }
        break;
        case 'disable':
          $stmt= $db->select('PLUGIN_TIMERS', 'ID = '.net::get('i'));
          if($stmt->rowCount() > 0) {
            $timer = $stmt->fetch();
            core::startModal('Disable '.$timer['NAME'], CONTROL.'?a=disable&i='.net::get('i'));
            echo 'Do you really want to disable this timer?';
            core::endModal('Disable', 'danger');
          } else {
            echo 'Not Found'.$output;
          }
        break;
        case 'reset':
          core::startModal('Reset Data', CONTROL.'?a=reset');
          echo '<div class=\'alert alert-danger\'>';
          echo '<strong><i class="fa fa-exclamation-triangle"></i> Warning!</strong> All data will be deleted!';
          echo '</div>';
          core::endModal('<i class="fa fa-exclamation-triangle"></i> RESET', 'danger');
        break;
        case 'del':
          $stmt= $db->select('PLUGIN_TIMERS', 'ID = '.net::get('i'));
          if($stmt->rowCount() > 0) {
            $timer = $stmt->fetch();
            core::startModal('Delete '.$timer['NAME'], CONTROL.'?a=del&i='.net::get('i'));
            echo 'Do you really want to delete this timer?';
            core::endModal('Delete', 'danger');
          } else {
            echo 'Not Found'.$output;
          }
        break;
        case 'add':
          core::startModal('Add Timer', CONTROL.'?a=add', 'POST');
          
          echo '<div class=\'form-group\'>';
          echo '<label class=\'col-sm-2\' for=\'name\'>Name</label>';
          echo '<div class=\'col-sm-10\'><input class=\'form-control\' data-fv-notempty id=\'name\' name=\'name\' placeholder=\'Name\' type=\'text\' value=\'\'></div>';
          echo '</div>';
          
          echo '<div class=\'form-group\'>';
          echo '<label class=\'col-sm-2\' for=\'value\'>Message</label>';
          echo '<div class=\'col-sm-10\'>';
          echo '<textarea class=\'form-control\' id=\'value\' data-fv-notempty name=\'value\' rows="5" placeholder="Message"></textarea>';
          echo '</div>';
          echo '</div>';
          
          echo '<br>';
          
          echo '<div class=\'form-group\'>';
          echo '<label class=\'col-sm-2\' for=\'mode\'>Mode</label>';
          echo '<div class=\'col-sm-10\'><select class=\'form-control\' id=\'mode\' name=\'mode\'>';
          echo '<option value=\'PING\'>Automatic</option>';
          echo '<option value=\'PRIVMSG\'>Chat Activity</option>';
          echo '</select></div>';
          echo '</div>';
          
          echo '<div class=\'form-group\'>';
          echo '<label class=\'col-sm-2\' for=\'schedule\'>Interval</label>';
          echo '<div class=\'col-sm-10\'><input class=\'form-control\' data-fv-notempty data-fv-numeric min=\'300\' id=\'schedule\' name=\'schedule\' placeholder=\'Interval in seconds\' type=\'text\' value=\'\'></div>';
          echo '</div>';
                        
          core::endModal('Save');
        break;
        case 'edit':
          $stmt= $db->select('PLUGIN_TIMERS', 'ID = '.net::get('i'));
          if($stmt->rowCount() > 0) {
            $timer = $stmt->fetch();
            core::startModal('Edit Timer', CONTROL.'?a=edit&i='.net::get('i'), 'POST');
            if($timer['ENABLED'] == 0) {
              echo '<div class=\'alert alert-warning\'>';
              echo '<strong>Warning!</strong> timer is disabled.';
              echo '</div>';
            }
            
            echo '<div class=\'form-group\'>';
            echo '<label class=\'col-sm-2\' for=\'name\'>Name</label>';
            echo '<div class=\'col-sm-10\'><input class=\'form-control\' data-fv-notempty id=\'name\' name=\'name\' placeholder=\'Name\' type=\'text\' value=\''.$timer['NAME'].'\'></div>';
            echo '</div>';
                          
            echo '<br>';
            
            echo '<div class=\'form-group\'>';
            echo '<label class=\'col-sm-2\' for=\'value\'>Message</label>';
            echo '<div class=\'col-sm-10\'>';
            echo '<textarea class=\'form-control\' id=\'value\' data-fv-notempty name=\'value\' rows="5" placeholder="Message">'.$timer['VALUE'].'</textarea>';
            echo '</div>';
            echo '</div>';
            
            echo '<br>';
          
            echo '<div class=\'form-group\'>';
            echo '<label class=\'col-sm-2\' for=\'mode\'>Mode</label>';
            echo '<div class=\'col-sm-10\'><select class=\'form-control\' id=\'mode\' name=\'mode\'>';
            echo '<option ';
            if($timer['MODE'] == 'PING') {
              echo 'selected';
            }
            echo ' value=\'PING\'>Automatic</option>';
            echo '<option ';
            if($user['MODE'] == 'PRIVMSG') {
              echo 'selected';
            }
            echo ' value=\'PRIVMSG\'>Chat Activity</option>';
            echo '</select></div>';
            echo '</div>';
            
            echo '<div class=\'form-group\'>';
            echo '<label class=\'col-sm-2\' for=\'schedule\'>Interval</label>';
            echo '<div class=\'col-sm-10\'><input class=\'form-control\' data-fv-notempty data-fv-numeric min=\'300\' id=\'schedule\' name=\'schedule\' placeholder=\'Interval in seconds\' type=\'text\' value=\''.$timer['SCHEDULE'].'\'></div>';
            echo '</div>';   
            core::endModal('Save');
          } else {
            echo 'Not Found'.$output;
          }
        break;
        default:
        net::fatal('Invalid function '.$output);
      }
    } else {
      net::fatal('No function '.$output);
    }
  } else {
    die(net::noaccess('No Access'));
  }
  
?>