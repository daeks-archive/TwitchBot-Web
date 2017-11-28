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
          $stmt= $db->select('PLUGIN_QUOTES', 'ID = '.net::get('i'));
          if($stmt->rowCount() > 0) {
            $quote = $stmt->fetch();
            core::startModal('Enable '.$quote['NAME'], CONTROL.'?a=enable&i='.net::get('i'));
            echo 'Do you really want to enable this quote?';
            core::endModal('Enable', 'success');
          } else {
            echo 'Not Found'.$output;
          }
        break;
        case 'disable':
          $stmt= $db->select('PLUGIN_QUOTES', 'ID = '.net::get('i'));
          if($stmt->rowCount() > 0) {
            $quote = $stmt->fetch();
            core::startModal('Disable '.$quote['NAME'], CONTROL.'?a=disable&i='.net::get('i'));
            echo 'Do you really want to disable this quote?';
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
          $stmt= $db->select('PLUGIN_QUOTES', 'ID = '.net::get('i'));
          if($stmt->rowCount() > 0) {
            $quote = $stmt->fetch();
            core::startModal('Delete '.$quote['NAME'], CONTROL.'?a=del&i='.net::get('i'));
            echo 'Do you really want to delete this quote?';
            core::endModal('Delete', 'danger');
          } else {
            echo 'Not Found'.$output;
          }
        break;
        case 'add':
          core::startModal('Add Quote', CONTROL.'?a=add', 'POST');
          
          echo '<div class=\'form-group\'>';
          echo '<label class=\'col-sm-2\' for=\'value\'>Quote</label>';
          echo '<div class=\'col-sm-10\'><input class=\'form-control\' data-fv-notempty id=\'value\' name=\'value\' placeholder=\'Quote\' type=\'text\' value=\'\'></div>';
          echo '</div>';           
            
          core::endModal('Save');
        break;
        case 'edit':
          $stmt= $db->select('PLUGIN_QUOTES', 'ID = '.net::get('i'));
          if($stmt->rowCount() > 0) {
            $quote = $stmt->fetch();
            core::startModal('Edit Quote', CONTROL.'?a=edit&i='.net::get('i'), 'POST');

            echo '<div class=\'form-group\'>';
            echo '<label class=\'col-sm-2\' for=\'value\'>Quote</label>';
            echo '<div class=\'col-sm-10\'><input class=\'form-control\' data-fv-notempty id=\'value\' name=\'value\' placeholder=\'Quote\' type=\'text\' value=\''.$quote['VALUE'].'\'></div>';
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