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
          $stmt = $db->select('PLUGIN_COMMANDS', $where.$limit);
          $data = array();
          $plugin = $db->select('PLUGINS', 'BOTID='.auth::getBotId().' and (CHANNELID='.auth::getChannelId()." or channelid=0) and NAME='".$module->id."' order by botid desc, channelid desc")->fetch();
          foreach($stmt->fetchAll() as $row) {
            $stmt = $db->select('COMMANDS', 'BOTID='.auth::getBotId().' and CHANNELID='.auth::getChannelId().' and PLUGINID='.$plugin['ID'].' and COMMANDID='.$row['ID']);
            if($stmt->rowCOunt() > 0) {
              $row['ENABLED'] = 0;
            } else {
              $row['ENABLED'] = 1;
            }
            array_push($data, $row);
          }
          net::data($db->query('SELECT COUNT(*) AS TOTAL FROM PLUGIN_COMMANDS WHERE '.$where)->fetch()['TOTAL'], json_encode($data));
        break;
        case 'enable':
          if(net::get('i') != '') {
            $db->delete('COMMANDS', 'COMMANDID='.net::get('i'));
            $status = bot::execute('reinit '.auth::getBotId().' '.auth::getChannelId());
            net::success('Sucessfully enabled','refreshTable();');
          } else {
            net::fatal('No ID '.$output);
          }
        break;
        case 'disable':
          if(net::get('i') != '') {
            $command = $db->select('PLUGIN_COMMANDS', 'ID='.net::get('i'))->fetch();
            $plugin = $db->select('PLUGINS', 'BOTID='.auth::getBotId().' and (CHANNELID='.auth::getChannelId()." or channelid=0) and NAME='".$module->id."' order by botid desc, channelid desc")->fetch();
            
            $fields = array();
            $fields['BOTID'] = auth::getBotId();
            $fields['CHANNELID'] = auth::getChannelId();
            $fields['PLUGINID'] = $plugin['ID'];
            $fields['COMMANDID'] = $command['ID'];
            $fields['NAME'] = $command['NAME'];
            $fields['VALUE'] = $command['NAME'];
            $fields['ENABLED'] = 0;
            $fields['INSERTBY'] = auth::getUserName();
            
            $db->insert('COMMANDS', $fields);
            $status = bot::execute('reinit '.auth::getBotId().' '.auth::getChannelId());
            net::success('Sucessfully disabled','refreshTable();');
          } else {
            net::fatal('No ID '.$output);
          }
        break;
        case 'add':
          $fields = array();
          $fields['NAME'] = net::post('name');
          $fields['BOTID'] = auth::getBotId();
          $fields['CHANNELID'] = auth::getChannelId();
          $fields['VALUE'] = net::post('value');
          $fields['INSERTBY'] = auth::getUserName();
        
          $db->insert('PLUGIN_COMMANDS', $fields);
          $status = bot::execute('reinit '.auth::getBotId().' '.auth::getChannelId());
          net::success('Sucessfully added','refreshTable();');
        break;
        case 'edit':
          if(net::get('i') != '') {
            $fields = array();
            $fields['NAME'] = net::post('name');
            $fields['VALUE'] = net::post('value');
            $fields['UPDATEBY'] = auth::getUserName();

            $db->update('PLUGIN_COMMANDS', $fields, 'ID='.net::get('i'));
            $status = bot::execute('reinit '.auth::getBotId().' '.auth::getChannelId());
            net::success('Sucessfully updated','refreshTable();');
          } else {
            net::fatal('No ID '.$output);
          }
        break;
        case 'reset':
          $db->delete('PLUGIN_COMMANDS', 'BOTID='.auth::getBotId().' and CHANNELID='.auth::getChannelId());
          $status = bot::execute('reinit '.auth::getBotId().' '.auth::getChannelId());
          net::success('Sucessfully reseted','refreshTable();');
        break;
        case 'del':
          if(net::get('i') != '') {
            $db->delete('PLUGIN_COMMANDS', 'ID='.net::get('i'));
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