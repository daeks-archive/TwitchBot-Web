<?php

  require_once(dirname(realpath(__DIR__)).DIRECTORY_SEPARATOR.'config.php');
  utils::construct();
  
  if(auth::hasAccess()) {
    $db = new db();
    
    $output = '';
    foreach($_GET as $key=>$value) {
      $output .= $key.'='.$value.';';
    }
  
    if(net::get('a') != '') {
      switch (net::get('a')) {
        case 'bot':
          if(net::get('i') != '') {
            auth::setBotId(net::get('i'));
            $stmt = $db->select('CHANNELS', 'BOTID='.net::get('i'));
            if($stmt->rowCount() == 1) {
              $channel = $stmt->fetch();
              auth::setChannelId($channel['ID']);
            } else {
              auth::setChannelId(0);
            }
            net::success('Sucessfully changed. Reloading...','location.reload();');
          } else {
            net::fatal('No ID '.$output);
          }
        break;
        case 'channel':
          if(net::get('i') != '') {
            auth::setChannelId(net::get('i'));
            net::success('Sucessfully changed. Reloading...','location.reload();');
          } else {
            net::fatal('No ID '.$output);
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