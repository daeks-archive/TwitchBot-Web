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
            $stmt= $db->select('USERS', 'ID = '.net::get('i'));
            if($stmt->rowCount() > 0) {
              $user = $stmt->fetch();
              core::startModal('Enable '.$user['NAME'], CONTROL.'?a=enable&i='.net::get('i'));
              echo 'Do you really want to enable this user?';
              core::endModal('Enable', 'success');
            } else {
              echo 'Not Found'.$output;
            }
          break;
          case 'disable':
            $stmt= $db->select('USERS', 'ID = '.net::get('i'));
            if($stmt->rowCount() > 0) {
              $user = $stmt->fetch();
              core::startModal('Disable '.$user['NAME'], CONTROL.'?a=disable&i='.net::get('i'));
              echo 'Do you really want to disable this user?';
              core::endModal('Disable', 'danger');
            } else {
              echo 'Not Found'.$output;
            }
          break;
          case 'del':
            $stmt= $db->select('USERS', 'ID = '.net::get('i'));
            if($stmt->rowCount() > 0) {
              $user = $stmt->fetch();
              core::startModal('Delete '.$user['NAME'], CONTROL.'?a=del&i='.net::get('i'));
              echo 'Do you really want to delete this user?';
              core::endModal('Delete', 'danger');
            } else {
              echo 'Not Found'.$output;
            }
          break;
          case 'edit':
            $stmt= $db->select('USERS', 'ID = '.net::get('i'));
            if($stmt->rowCount() > 0) {
              $user = $stmt->fetch();
              core::startModal('Edit User', CONTROL.'?a=edit&i='.net::get('i'), 'POST');
              if($user['ENABLED'] == 0) {
                echo '<div class="alert alert-warning">';
                echo '<strong>Warning!</strong> User is disabled.';
                echo '</div>';
              }
              echo '<div class=\'form-group\'>';
              echo '<label class=\'col-sm-2\' for=\'name\'>Name</label>';
              echo '<div class=\'col-sm-10\'><input class=\'form-control\' data-fv-notempty id=\'name\' name=\'name\' placeholder=\'Name\' type=\'text\' value=\''.$user['NAME'].'\'></div>';
              echo '</div>';
              echo '<div class=\'form-group\'>';
              echo '<label class=\'col-sm-2\' for=\'email\'>Email</label>';
              echo '<div class=\'col-sm-10\'><input class=\'form-control\' data-fv-notempty id=\'email\' name=\'email\' placeholder=\'Email\' type=\'email\' value=\''.$user['EMAIL'].'\'></div>';
              echo '</div>';
              
              echo '<br>';
              
              echo '<div class=\'form-group\'>';
              echo '<label class=\'col-sm-2\' for=\'logo\'>Logo</label>';
              echo '<div class=\'col-sm-10\'>';
              echo '<input class=\'form-control\' data-fv-uri data-fv-uri-allowlocal=\'true\' data-fv-uri-allowemptyprotocol=\'true\' id=\'logo\' name=\'logo\' placeholder=\'Logo Icon URL\' type=\'text\' value=\''.$user['LOGO'].'\'>';
              echo '</div>';
              echo '</div>';
            
              echo '<div class=\'form-group\'>';
              echo '<label class=\'col-sm-2\' for=\'banner\'>Banner</label>';
              echo '<div class=\'col-sm-10\'>';
              echo '<input class=\'form-control\' data-fv-uri data-fv-uri-allowlocal=\'true\' data-fv-uri-allowemptyprotocol=\'true\' id=\'banner\' name=\'banner\' placeholder=\'Banner Icon URL\' type=\'text\' value=\''.$user['BANNER'].'\'>';
              echo '</div>';
              echo '</div>';
              
              echo '<br>';
              
              echo '<div class=\'form-group\'>';
              echo '<label class=\'col-sm-2\' for=\'token\'>OAuth</label>';
              echo '<div class=\'col-sm-10\'>';
              echo '<input class=\'form-control\' data-fv-notempty id=\'token\' name=\'token\' placeholder=\'OAuth Token\' type=\'text\' value=\''.$user['OAUTH'].'\'>';
              echo '</div>';
              echo '</div>';
              
              echo '<br>';
              
              echo '<div class=\'form-group\'>';
              echo '<label class=\'col-sm-2\' for=\'enabled\'>Status</label>';
              echo '<div class=\'col-sm-10\'><select class=\'form-control\' id=\'enabled\' name=\'enabled\'>';
              echo '<option ';
              if($user['ENABLED'] == 0) {
                echo 'selected';
              }
              echo ' value=\'0\'>Disabled</option>';
              echo '<option ';
              if($user['ENABLED'] == 1) {
                echo 'selected';
              }
              echo ' value=\'1\'>Enabled</option>';
              echo '</select></div>';
              echo '</div>';
              echo '<div class=\'form-group\'>';
              echo '<label class=\'col-sm-12\' for=\'level\'>Access Level</label>';
              echo '<div class=\'col-sm-12\'><select class=\'form-control\' id=\'level\' name=\'level\'>';
              echo '<option value=\'\'>User</option>';
              echo '<option ';
              if($user['LEVEL'] == auth::$owner) {
                echo 'selected';
              }
              echo ' value=\''.auth::$owner.'\'>Owner</option>';
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