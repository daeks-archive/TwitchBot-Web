<?php

  require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');
  
  if(auth::hasAccess()) {
    $output = '';
    foreach($_GET as $key=>$value) {
      $output .= $key.'='.$value.';';
    }
  
    if(net::get('a') != '') {
      switch (net::get('a')) {
        case 'init':
          $where = 'NAME="PRIVMSG" and type="READ" and BOTID='.auth::getBotId().' and CHANNELID='.auth::getChannelId();
          if(net::get('q') != '') {
            $where .= " and (VALUE like '%".net::get('q')."%' OR INSERTBY like '%".net::get('q')."%')";
          }
          //if(net::get('s') != '' && net::get('o') != '') {
          //  $where .= " order by ".net::get('s')." ".net::get('o');
          //}
          $where .= " order by ID DESC";
          $limit = '';
          if(net::get('l') != '' && net::get('p') != '') {
            $limit = " limit ".net::get('p').", ".net::get('l');
          }     
          $stmt = $db->select('LOGS', $where.$limit);
          $channel = $db->select('CHANNELS', 'BOTID='.auth::getBotId().' and ID='.auth::getChannelId())->fetch();
          $data = array();
          foreach($stmt->fetchAll() as $row) {
            if(isset($channel['NAME'])) {
              $row['CHANNEL'] = $channel['NAME'];
            } else {
              $row['CHANNEL'] = '';
            }
            array_push($data, $row);
          }   
          net::data($db->query('SELECT COUNT(*) AS TOTAL FROM LOGS WHERE '.$where)->fetch()['TOTAL'], json_encode($data));
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