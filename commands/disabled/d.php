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
        case 'reset':
          core::startModal('Reset Data', CONTROL.'?a=reset');
          echo '<div class=\'alert alert-danger\'>';
          echo '<strong><i class="fa fa-exclamation-triangle"></i> Warning!</strong> All data will be deleted!';
          echo '</div>';
          core::endModal('<i class="fa fa-exclamation-triangle"></i> RESET', 'danger');
        break;
        case 'add':
          core::startModal('Add Alias', CONTROL.'?a=add', 'POST');
          
          echo '<div class=\'form-group\'>';
          echo '<label class=\'col-sm-2\' for=\'name\'>Command</label>';
          echo '<div class=\'col-sm-10\'><input class=\'form-control\' data-fv-notempty id=\'name\' name=\'name\' placeholder=\'Command\' type=\'text\' value=\'\'></div>';
          echo '</div>';
            
          core::endModal('Save');
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