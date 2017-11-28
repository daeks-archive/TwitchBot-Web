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
          case 'enable':
            $stmt= $db->select('CHANNELS', 'ID = '.net::get('i'));
            if($stmt->rowCount() > 0) {
              $channel = $stmt->fetch();
              core::startModal('Enable '.$channel['NAME'], CONTROL.'?a=enable&i='.net::get('i'));
              echo 'Do you really want to enable this channel?';
              core::endModal('Enable', 'success');
            } else {
              echo 'Not Found'.$output;
            }
          break;
          case 'disable':
            $stmt= $db->select('CHANNELS', 'ID = '.net::get('i'));
            if($stmt->rowCount() > 0) {
              $channel = $stmt->fetch();
              core::startModal('Disable '.$channel['NAME'], CONTROL.'?a=disable&i='.net::get('i'));
              echo 'Do you really want to disable this channel?';
              core::endModal('Disable', 'danger');
            } else {
              echo 'Not Found'.$output;
            }
          break;
          case 'update':
            $stmt= $db->select('CHANNELS', 'ID = '.net::get('i'));
            if($stmt->rowCount() > 0) {
              $channel = $stmt->fetch();
              core::startModal('Update '.$channel['NAME'], CONTROL.'?a=update&i='.net::get('i'));
              echo 'Do you really want to update this channel from Twitch?';
              core::endModal('Update', 'default');
            } else {
              echo 'Not Found'.$output;
            }
          break;
          case 'del':
            $stmt= $db->select('CHANNELS', 'ID = '.net::get('i'));
            if($stmt->rowCount() > 0) {
              $channel = $stmt->fetch();
              core::startModal('Delete '.$channel['NAME'], CONTROL.'?a=del&i='.net::get('i'));
              echo 'Do you really want to delete this channel?';
              core::endModal('Delete', 'danger');
            } else {
              echo 'Not Found'.$output;
            }
          break;
          case 'add':
            core::startModal('Add Channel', CONTROL.'?a=add', 'POST');
            
            echo '<div class=\'form-group\'>';
            echo '<label class=\'col-sm-2\' for=\'name\'>Name</label>';
            echo '<div class=\'col-sm-10\'><input class=\'form-control\' data-fv-notempty id=\'name\' name=\'name\' placeholder=\'Name\' type=\'text\' value=\'\'></div>';
            echo '</div>';
            
            echo '<div class=\'form-group row\'>';
            echo '<label class=\'col-sm-2\' for=\'mute\'></label>';
            echo '<div class=\'col-sm-10\'><div class=\'checkbox\'><label><input type=\'checkbox\' id=\'mute\' name=\'mute\'> Mute Channel</label></div></div>';
            echo '</div>';
              
            core::endModal('Save');
          break;
          case 'edit':
            $stmt= $db->select('CHANNELS', 'ID = '.net::get('i'));
            if($stmt->rowCount() > 0) {
              $channel = $stmt->fetch();
              core::startModal('Edit Channel', CONTROL.'?a=edit&i='.net::get('i'), 'POST');
              if($channel['ENABLED'] == 0) {
                echo '<div class=\'alert alert-warning\'>';
                echo '<strong>Warning!</strong> Channel is disabled.';
                echo '</div>';
              }
                            
              $muted = 0;
              $stmt = $db->select('CONFIG', 'BOTID='.auth::getBotId().' and CHANNELID='.net::get('i')." and NAME='MUTE'");
              if($stmt->rowCount() > 0) {
                $config = $stmt->fetch();
                $muted = $config['VALUE'];
              }
              if($muted == 1) {
                echo '<div class=\'alert alert-warning\'>';
                echo '<strong>Warning!</strong> Channel is muted.';
                echo '</div>';
              }
              
              echo '<div class=\'form-group\'>';
              echo '<label class=\'col-sm-2\' for=\'name\'>Name</label>';
              echo '<div class=\'col-sm-10\'><input class=\'form-control\' data-fv-notempty id=\'name\' name=\'name\' placeholder=\'Name\' type=\'text\' value=\''.$channel['NAME'].'\'></div>';
              echo '</div>';
                            
              echo '<br>';
              
              echo '<div class=\'form-group\'>';
              echo '<label class=\'col-sm-2\' for=\'logo\'>Logo</label>';
              echo '<div class=\'col-sm-10\'>';
              echo '<input class=\'form-control\' data-fv-uri data-fv-uri-allowlocal=\'true\' data-fv-uri-allowemptyprotocol=\'true\' id=\'logo\' name=\'logo\' placeholder=\'Logo Icon URL\' type=\'text\' value=\''.$channel['LOGO'].'\'>';
              echo '</div>';
              echo '</div>';
            
              echo '<div class=\'form-group\'>';
              echo '<label class=\'col-sm-2\' for=\'banner\'>Banner</label>';
              echo '<div class=\'col-sm-10\'>';
              echo '<input class=\'form-control\' data-fv-uri data-fv-uri-allowlocal=\'true\' data-fv-uri-allowemptyprotocol=\'true\' id=\'banner\' name=\'banner\' placeholder=\'Banner Icon URL\' type=\'text\' value=\''.$channel['BANNER'].'\'>';
              echo '</div>';
              echo '</div>';
              
              echo '<br>';
              
              echo '<div class=\'form-group\'>';
              echo '<label class=\'col-sm-2\' for=\'enabled\'>Status</label>';
              echo '<div class=\'col-sm-10\'><select class=\'form-control\' id=\'enabled\' name=\'enabled\'>';
              echo '<option ';
              if($channel['ENABLED'] == 0) {
                echo 'selected';
              }
              echo ' value=\'0\'>Disabled</option>';
              echo '<option ';
              if($channel['ENABLED'] == 1) {
                echo 'selected';
              }
              echo ' value=\'1\'>Enabled</option>';
              echo '</select></div>';
              echo '</div>';    
              
              echo '<div class=\'form-group\'>';
              echo '<label class=\'col-sm-2\' for=\'mute\'>Muted Channel</label>';
              echo '<div class=\'col-sm-10\'><select class=\'form-control\' id=\'mute\' name=\'mute\'>';
              echo '<option ';
              if($muted == 0) {
                echo 'selected';
              }
              echo ' value=\'0\'>Disabled</option>';
              echo '<option ';
              if($muted == 1) {
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