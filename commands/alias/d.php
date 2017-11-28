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
          $stmt= $db->select('COMMANDS', 'ID = '.net::get('i'));
          if($stmt->rowCount() > 0) {
            $command = $stmt->fetch();
            core::startModal('Enable '.$command['NAME'], CONTROL.'?a=enable&i='.net::get('i'));
            echo 'Do you really want to enable this command?';
            core::endModal('Enable', 'success');
          } else {
            echo 'Not Found'.$output;
          }
        break;
        case 'disable':
          $stmt= $db->select('COMMANDS', 'ID = '.net::get('i'));
          if($stmt->rowCount() > 0) {
            $command = $stmt->fetch();
            core::startModal('Disable '.$command['NAME'], CONTROL.'?a=disable&i='.net::get('i'));
            echo 'Do you really want to disable this command?';
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
          $stmt= $db->select('COMMANDS', 'ID = '.net::get('i'));
          if($stmt->rowCount() > 0) {
            $command = $stmt->fetch();
            core::startModal('Delete '.$command['NAME'], CONTROL.'?a=del&i='.net::get('i'));
            echo 'Do you really want to delete this command?';
            core::endModal('Delete', 'danger');
          } else {
            echo 'Not Found'.$output;
          }
        break;
        case 'add':
          core::startModal('Add Alias', CONTROL.'?a=add', 'POST');           
          echo '<div class=\'form-group\'>';
          echo '<label class=\'col-sm-2\' for=\'name\'>Alias</label>';
          echo '<div class=\'col-sm-10\'><input class=\'form-control\' data-fv-notempty id=\'name\' name=\'name\' placeholder=\'Alias\' type=\'text\' value=\'\'></div>';
          echo '</div>';
          
          echo '<div class=\'form-group\'>';
          echo '<label class=\'col-sm-2\' for=\'value\'>Command</label>';
          echo '<div class=\'col-sm-10\'><input class=\'form-control\' data-fv-notempty id=\'value\' name=\'value\' placeholder=\'Command\' type=\'text\' value=\'\'></div>';
          echo '</div>';
          core::endModal('Save');
        break;
        case 'edit':
          $stmt= $db->select('COMMANDS', 'ID = '.net::get('i'));
          if($stmt->rowCount() > 0) {
            $command = $stmt->fetch();
            core::startModal('Edit Alias', CONTROL.'?a=edit&i='.net::get('i'), 'POST');
            if($command['ENABLED'] == 0) {
              echo '<div class=\'alert alert-warning\'>';
              echo '<strong>Warning!</strong> Alias is disabled.';
              echo '</div>';
            }
            
            echo '<div class=\'form-group\'>';
            echo '<label class=\'col-sm-2\' for=\'name\'>Alias</label>';
            echo '<div class=\'col-sm-10\'><input class=\'form-control\' data-fv-notempty id=\'name\' name=\'name\' placeholder=\'Alias\' type=\'text\' value=\''.$command['NAME'].'\'></div>';
            echo '</div>';
                          
            echo '<br>';
            
            echo '<div class=\'form-group\'>';
            echo '<label class=\'col-sm-2\' for=\'value\'>Command</label>';
            echo '<div class=\'col-sm-10\'>';
            echo '<input class=\'form-control\' id=\'value\' data-fv-notempty name=\'value\' placeholder=\'Command\' type=\'text\' value=\''.$command['VALUE'].'\'>';
            echo '</div>';
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