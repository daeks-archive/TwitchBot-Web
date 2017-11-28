<?php

  require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');
   
  if(auth::hasAccess()) {
    if(!auth::checkAccess(auth::$owner)) {
      die(net::noaccess('No Access'));
    } else {
      $output = '';
      foreach($_GET as $key=>$value) {
        $output .= $key.'='.$value.';';
      }
    
      if(net::get('a') != '') {
        switch (net::get('a')) {
          case 'start':
            $stmt= $db->select('BOTS', 'ID = '.net::get('i'));
            if($stmt->rowCount() > 0) {
              $bot = $stmt->fetch();
              core::startModal('Start '.$bot['NAME'], CONTROL.'?a=start&i='.net::get('i'));
              echo 'Do you really want to start this bot?';
              core::endModal('START', 'success');
            } else {
              echo 'Not Found'.$output;
            }
          break;
          case 'stop':
            $stmt= $db->select('BOTS', 'ID = '.net::get('i'));
            if($stmt->rowCount() > 0) {
              $bot = $stmt->fetch();
              core::startModal('Stop '.$bot['NAME'], CONTROL.'?a=stop&i='.net::get('i'));
              echo 'Do you really want to stop this bot?';
              core::endModal('STOP', 'danger');
            } else {
              echo 'Not Found'.$output;
            }
          break;
          case 'restart':
            $stmt= $db->select('BOTS', 'ID = '.net::get('i'));
            if($stmt->rowCount() > 0) {
              $bot = $stmt->fetch();
              core::startModal('Restart '.$bot['NAME'], CONTROL.'?a=restart&i='.net::get('i'));
              echo 'Do you really want to restart this bot?';
              core::endModal('Restart', 'danger');
            } else {
              echo 'Not Found'.$output;
            }
          break;
          case 'enable':
            $stmt= $db->select('BOTS', 'ID = '.net::get('i'));
            if($stmt->rowCount() > 0) {
              $bot = $stmt->fetch();
              core::startModal('Enable '.$bot['NAME'], CONTROL.'?a=enable&i='.net::get('i'));
              echo 'Do you really want to enable this bot?';
              core::endModal('Enable', 'success');
            } else {
              echo 'Not Found'.$output;
            }
          break;
          case 'disable':
            $stmt= $db->select('BOTS', 'ID = '.net::get('i'));
            if($stmt->rowCount() > 0) {
              $bot = $stmt->fetch();
              core::startModal('Disable '.$bot['NAME'], CONTROL.'?a=disable&i='.net::get('i'));
              echo 'Do you really want to disable this bot?';
              core::endModal('Disable', 'danger');
            } else {
              echo 'Not Found'.$output;
            }
          break;
          case 'del':
            $stmt= $db->select('BOTS', 'ID = '.net::get('i'));
            if($stmt->rowCount() > 0) {
              $bot = $stmt->fetch();
              core::startModal('Delete '.$bot['NAME'], CONTROL.'?a=del&i='.net::get('i'));
              echo 'Do you really want to delete this bot?';
              core::endModal('Delete', 'danger');
            } else {
              echo 'Not Found'.$output;
            }
          break;
          case 'update':
            $stmt= $db->select('BOTS', 'ID = '.net::get('i'));
            if($stmt->rowCount() > 0) {
              $bot = $stmt->fetch();
              $stmt2 = $db->select('USERS', "lower(NAME) = '".strtolower($bot['NAME'])."'");
              if($stmt2->rowCount() > 0) {
                $user = $stmt2->fetch();
                core::startModal('Update '.$bot['NAME'], CONTROL.'?a=update&i='.net::get('i').'&ui='.$user['ID']);
                echo 'Do you really want to update this bot from user '.$user['NAME'].'?';
                core::endModal('Update', 'default');
              } else {
                echo 'Not Found'.$output;
              }
            } else {
              echo 'Not Found'.$output;
            }
          break;
          case 'add':
            core::startModal('Add Bot', CONTROL.'?a=add', 'POST');
            
            echo '<div class=\'form-group\'>';
            echo '<label class=\'col-sm-2\' for=\'id\'>Name</label>';
            echo '<div class=\'col-sm-10\'><select data-fv-notempty class=\'form-control\' id=\'id\' name=\'id\'>';
            foreach($db->select('USERS', 'ID > 0 and ENABLED=1')->fetchAll() as $user) {
              $stmt = $db->select('BOTS', "lower(NAME) ='".strtolower($user['NAME'])."'");
              if($stmt->rowCount() == 0) {
                echo '<option selected value=\''.$user['ID'].'\'>'.$user['NAME'].'</option>';
              }
            }
            echo '</select></div>';
            echo '</div>';
            
            echo '<div class=\'form-group\'>';
            echo '<label class=\'col-sm-2\' for=\'color\'>Color</label>';
            echo '<div class=\'col-sm-10\'><select class=\'form-control\' id=\'color\' name=\'color\'>';
            foreach($colors as $color) {
              echo '<option value=\''.$color.'\'>'.$color.'</option>';
            }
            echo '</select></div>';
            echo '</div>';
            
            echo '<br>';
            
            echo '<div class=\'form-group\'>';
            echo '<label class=\'col-sm-2\' for=\'owner\'>Owner</label>';
            echo '<div class=\'col-sm-10\'>';
            echo '<input class=\'form-control\' data-fv-notempty id=\'owner\' name=\'owner\' placeholder=\'Owner\' type=\'text\' value=\''.auth::getUserName().'\'>';
            echo '</div>';
            echo '</div>';
            
            core::endModal('Save');
          break;
          case 'edit':
            $stmt= $db->select('BOTS', 'ID = '.net::get('i'));
            if($stmt->rowCount() > 0) {
              $bot = $stmt->fetch();
              core::startModal('Edit Bot', CONTROL.'?a=edit&i='.net::get('i'), 'POST');
              if($bot['ENABLED'] == 0) {
                echo '<div class="alert alert-warning">';
                echo '<strong>Warning!</strong> Bot is disabled.';
                echo '</div>';
              }
              echo '<div class=\'form-group\'>';
              echo '<label class=\'col-sm-2\' for=\'name\'>Name</label>';
              echo '<div class=\'col-sm-10\'><input class=\'form-control\' data-fv-notempty id=\'name\' name=\'name\' placeholder=\'Name\' type=\'text\' value=\''.$bot['NAME'].'\'></div>';
              echo '</div>';
              
              echo '<div class=\'form-group\'>';
              echo '<label class=\'col-sm-2\' for=\'color\'>Color</label>';
              echo '<div class=\'col-sm-10\'><select class=\'form-control\' id=\'color\' name=\'color\'>';
              foreach($colors as $color) {
                echo '<option ';
                if($bot['COLOR'] == $color) {
                  echo 'selected';
                }
                echo ' value=\''.$color.'\'>'.$color.'</option>';
              }
              echo '</select></div>';
              echo '</div>';
              
              echo '<br>';
              
              echo '<div class=\'form-group\'>';
              echo '<label class=\'col-sm-2\' for=\'logo\'>Logo</label>';
              echo '<div class=\'col-sm-10\'>';
              echo '<input class=\'form-control\' data-fv-uri data-fv-uri-allowlocal=\'true\' data-fv-uri-allowemptyprotocol=\'true\' id=\'logo\' name=\'logo\' placeholder=\'Logo Icon URL\' type=\'text\' value=\''.$bot['LOGO'].'\'>';
              echo '</div>';
              echo '</div>';
            
              echo '<div class=\'form-group\'>';
              echo '<label class=\'col-sm-2\' for=\'banner\'>Banner</label>';
              echo '<div class=\'col-sm-10\'>';
              echo '<input class=\'form-control\' data-fv-uri data-fv-uri-allowlocal=\'true\' data-fv-uri-allowemptyprotocol=\'true\' id=\'banner\' name=\'banner\' placeholder=\'Banner Icon URL\' type=\'text\' value=\''.$bot['BANNER'].'\'>';
              echo '</div>';
              echo '</div>';
              
              echo '<br>';
              
              echo '<div class=\'form-group\'>';
              echo '<label class=\'col-sm-2\' for=\'token\'>OAuth</label>';
              echo '<div class=\'col-sm-10\'>';
              echo '<input class=\'form-control\' data-fv-notempty id=\'token\' name=\'token\' placeholder=\'OAuth Token\' type=\'text\' value=\''.$bot['OAUTH'].'\'>';
              echo '</div>';
              echo '</div>';
              echo '<div class=\'form-group\'>';
              echo '<label class=\'col-sm-2\' for=\'owner\'>Owner</label>';
              echo '<div class=\'col-sm-10\'>';
              echo '<input class=\'form-control\' data-fv-notempty id=\'owner\' name=\'owner\' placeholder=\'Owner\' type=\'text\' value=\''.$bot['OWNER'].'\'>';
              echo '</div>';
              echo '</div>';
              
              echo '<br>';
              
              echo '<div class=\'form-group\'>';
              echo '<label class=\'col-sm-2\' for=\'enabled\'>Status</label>';
              echo '<div class=\'col-sm-10\'><select class=\'form-control\' id=\'enabled\' name=\'enabled\'>';
              echo '<option ';
              if($bot['ENABLED'] == 0) {
                echo 'selected';
              }
              echo ' value=\'0\'>Disabled</option>';
              echo '<option ';
              if($bot['ENABLED'] == 1) {
                echo 'selected';
              }
              echo ' value=\'1\'>Enabled</option>';
              echo '</select></div>';
              echo '</div>';
              echo '<div class=\'form-group\'>';
              echo '<label class=\'col-sm-2\' for=\'autostart\'>Autostart</label>';
              echo '<div class=\'col-sm-10\'><select class=\'form-control\' id=\'autostart\' name=\'autostart\'>';
              echo '<option ';
              if($bot['AUTOSTART'] == 0) {
                echo 'selected';
              }
              echo ' value=\'0\'>Disabled</option>';
              echo '<option ';
              if($bot['AUTOSTART'] == 1) {
                echo 'selected';
              }
              echo ' value=\'1\'>Enabled</option>';
              echo '</select></div>';
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
    }
  } else {
    die(net::noaccess('No Access'));
  }
  
?>