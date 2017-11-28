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
          $where = 'ID>0 and BOTID='.auth::getBotId().' and CHANNELID='.auth::getChannelId();
          if(net::get('q') != '') {
            $where .= " and VALUE like '%".net::get('q')."%'";
          }
          if(net::get('s') != '' && net::get('o') != '') {
            $where .= " order by ".net::get('s')." ".net::get('o');
          } else {
            $where .= " order by inserted desc";
          }
          $limit = '';
          if(net::get('l') != '' && net::get('p') != '') {
            $limit = " limit ".net::get('p').", ".net::get('l');
          }     
          $stmt = $db->select('PLUGIN_QUOTES', $where.$limit);
          $data = array();
          foreach($stmt->fetchAll() as $row) {
            array_push($data, $row);
          }
          net::data($db->query('SELECT COUNT(*) AS TOTAL FROM PLUGIN_QUOTES WHERE '.$where)->fetch()['TOTAL'], json_encode($data));
        break;
        case 'add':
          $fields = array();
          $fields['BOTID'] = auth::getBotId();
          $fields['CHANNELID'] = auth::getChannelId();
          $fields['VALUE'] = net::post('value');
          $fields['INSERTBY'] = auth::getUserName();
        
          $db->insert('PLUGIN_QUOTES', $fields);
          $status = bot::execute('reinit '.auth::getBotId().' '.auth::getChannelId());
          net::success('Sucessfully added','refreshTable();');
        break;
        case 'edit':
          if(net::get('i') != '') {
            $fields = array();
            $fields['VALUE'] = net::post('value');
            $fields['UPDATEBY'] = auth::getUserName();

            $db->update('PLUGIN_QUOTES', $fields, 'ID='.net::get('i'));
            $status = bot::execute('reinit '.auth::getBotId().' '.auth::getChannelId());
            net::success('Sucessfully updated','refreshTable();');
          } else {
            net::fatal('No ID '.$output);
          }
        break;
        case 'reset':
          $db->delete('PLUGIN_QUOTES', 'BOTID='.auth::getBotId().' and NAME=VALUE and CHANNELID='.auth::getChannelId());
          $status = bot::execute('reinit '.auth::getBotId().' '.auth::getChannelId());
          net::success('Sucessfully reseted','refreshTable();');
        break;
        case 'del':
          if(net::get('i') != '') {
            $db->delete('PLUGIN_QUOTES', 'ID='.net::get('i'));
            $status = bot::execute('reinit '.auth::getBotId().' '.auth::getChannelId());
            net::success('Sucessfully deleted','refreshTable();');
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