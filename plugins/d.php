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
          if(is_numeric(net::get('i'))) {
            $stmt= $db->select('PLUGINS', 'ID = '.net::get('i'));
            if($stmt->rowCount() > 0) {
              $plugin = $stmt->fetch();
              core::startModal('Enable '.$plugin['NAME'], CONTROL.'?a=enable&i='.net::get('i'));
              echo 'Do you really want to enable this plugin?';
              core::endModal('Enable', 'success');
            } else {
              echo 'Not Found'.$output;
            }
          } else {
            core::startModal('Enable '.net::get('i'), CONTROL.'?a=enable&i='.net::get('i'));
            echo 'Do you really want to enable this plugin?';
            core::endModal('Enable', 'success');
          }
        break;
        case 'disable':
          if(is_numeric(net::get('i'))) {
            $stmt= $db->select('PLUGINS', 'ID = '.net::get('i'));
            if($stmt->rowCount() > 0) {
              $plugin = $stmt->fetch();
              core::startModal('Disable '.$plugin['NAME'], CONTROL.'?a=disable&i='.net::get('i'));
              echo 'Do you really want to disable this plugin?';
              core::endModal('Disable', 'danger');
            } else {
              echo 'Not Found'.$output;
            }
          } else {
            core::startModal('Disable '.net::get('i'), CONTROL.'?a=disable&i='.net::get('i'));
            echo 'Do you really want to disable this plugin?';
            core::endModal('Disable', 'danger');
          }
        break;
        case 'config':
          if(is_numeric(net::get('i'))) {
            $stmt= $db->select('PLUGINS', 'ID = '.net::get('i'));
            if($stmt->rowCount() > 0) {
              $plugin = $stmt->fetch();
              if(file_exists(BASE.DIRECTORY_SEPARATOR.$module->id.DIRECTORY_SEPARATOR.$plugin['NAME'].CONFIG)) {
                require(BASE.DIRECTORY_SEPARATOR.$module->id.DIRECTORY_SEPARATOR.$plugin['NAME'].CONFIG);
              }
            } else {
              echo 'Not Found'.$output;
            }
          } else {
            if(file_exists(BASE.DIRECTORY_SEPARATOR.$module->id.DIRECTORY_SEPARATOR.net::get('i').CONFIG)) {
              require(BASE.DIRECTORY_SEPARATOR.$module->id.DIRECTORY_SEPARATOR.net::get('i').CONFIG);
            }
          }
          break;
        case 'del':
            $stmt= $db->select('PLUGINS', 'ID = '.net::get('i'));
            if($stmt->rowCount() > 0) {
              $plugin = $stmt->fetch();
              core::startModal('Delete '.$plugin['NAME'], CONTROL.'?a=del&i='.net::get('i'));
              echo 'Do you really want to delete this plugin?';
              core::endModal('Delete', 'danger');
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