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
          $where = 'ID>0 and BOTID='.auth::getBotId().' and ENABLED=0 and CHANNELID='.auth::getChannelId();
          if(net::get('q') != '') {
            $where .= " and (NAME like '%".net::get('q')."%' or VALUE like '%".net::get('q')."%')";
          }
          if(net::get('s') != '' && net::get('o') != '') {
            $where .= " order by ".net::get('s')." ".net::get('o');
          } else {
            $where .= " order by name asc";
          }
          $limit = '';
          if(net::get('l') != '' && net::get('p') != '') {
            $limit = " limit ".net::get('p').", ".net::get('l');
          }      
          $stmt = $db->select('COMMANDS', $where.$limit);
          $data = array();
          foreach($stmt->fetchAll() as $row) {
            array_push($data, $row);
          }
          net::data($db->query('SELECT COUNT(*) AS TOTAL FROM COMMANDS WHERE '.$where)->fetch()['TOTAL'], json_encode($data));
        break;
        case 'reset':
          $db->delete('COMMANDS', 'BOTID='.auth::getBotId().' and NAME=VALUE and CHANNELID='.auth::getChannelId());
          $status = bot::execute('reinit '.auth::getBotId().' '.auth::getChannelId());
          net::success('Sucessfully reseted','refreshTable();');
        break;
        case 'enable':
          if(net::get('i') != '') {
            $db->delete('COMMANDS', 'ID='.net::get('i'));
            $status = bot::execute('reinit '.auth::getBotId().' '.auth::getChannelId());
            net::success('Sucessfully enabled','refreshTable();');
          } else {
            net::fatal('No ID '.$output);
          }
        break;
        case 'add':
          $fields = array();
          $fields['NAME'] = net::post('name');
          $fields['BOTID'] = auth::getBotId();
          $fields['CHANNELID'] = auth::getChannelId();
          $fields['VALUE'] = net::post('name');
          $fields['ENABLED'] = 0;
          $fields['INSERTBY'] = auth::getUserName();
        
          $db->insert('COMMANDS', $fields);
          $status = bot::execute('reinit '.auth::getBotId().' '.auth::getChannelId());
          net::success('Sucessfully added','refreshTable();');
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