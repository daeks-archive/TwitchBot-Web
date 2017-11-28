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
          case 'init':
            $where = 'ID>0 and BOTID='.auth::getBotId();
            if(net::get('q') != '') {
              $where .= " and NAME like '%".net::get('q')."%'";
            }
            if(net::get('s') != '' && net::get('o') != '') {
              $where .= " order by ".net::get('s')." ".net::get('o');
            }
            $limit = '';
            if(net::get('l') != '' && net::get('p') != '') {
              $limit = " limit ".net::get('p').", ".net::get('l');
            }     
            $stmt = $db->select('CHANNELS', $where.$limit);
            $data = array();
            foreach($stmt->fetchAll() as $row) {
              $status = bot::execute('status '.auth::getBotId().' '.$row['ID']);
              if(isset($status->state)) {
                $row['STATE'] = $status->state;
              } else {
                $row['STATE'] = 'FAILED';
              }
              $muted = 0;
              $stmt2 = $db->select('CONFIG', 'BOTID='.auth::getBotId().' and CHANNELID='.$row['ID']." and NAME='MUTE'");
              if($stmt2->rowCount() > 0) {
                $config = $stmt2->fetch();
                $row['MUTE'] = $config['VALUE'];
              } else {
                $row['MUTE'] = 0;
              }
              array_push($data, $row);
            }
            net::data($db->query('SELECT COUNT(*) AS TOTAL FROM CHANNELS WHERE '.$where)->fetch()['TOTAL'], json_encode($data));
          break;
          case 'enable':
            if(net::get('i') != '') {
              $db->update('CHANNELS', array('ENABLED' => 1, 'UPDATEBY' => auth::getUserName()), 'ID='.net::get('i'));
              net::success('Sucessfully enabled','refreshTable();');
            } else {
              net::fatal('No ID '.$output);
            }
          break;
          case 'disable':
            if(net::get('i') != '') {
              $db->update('CHANNELS', array('ENABLED' => 0, 'UPDATEBY' => auth::getUserName()), 'ID='.net::get('i'));
              net::success('Sucessfully disabled','refreshTable();');
            } else {
              net::fatal('No ID '.$output);
            }
          break;
          case 'add':
            $channel = twitch::select(null, 'channels/'.net::post('name'));
            if(isset($channel->error)) {
              net::fatal($channel->message);
            } else {
              $fields = array();
              $fields['NAME'] = '#'.net::post('name');
              $fields['BOTID'] = auth::getBotId();
              
              $channel = twitch::select(null, 'channels/'.net::post('name'));
              if(isset($channel->logo)) {
                $fields['LOGO'] = $channel->logo;
              }
              if(isset($channel->banner)) {
                $fields['BANNER'] = $channel->banner;
              }
              
              $fields['INSERTBY'] = auth::getUserName();
            
              $channelid = $db->insert('CHANNELS', $fields);
              
              if(net::post('mute') == 1) {
                $config = array();
                $config['BOTID'] = auth::getBotId();
                $config['CHANNELID'] = $channelid;
                $config['NAME'] = 'MUTE';
                $config['VALUE'] = 1;
                $config['INSERTBY'] = auth::getUserName();
                $db->insert('CONFIG', $config);
              }
             
              net::success('Sucessfully added','refreshTable();');
            }
          break;
          case 'update':
            if(net::get('i') != '') {
              $channel = $db->select('CHANNELS', 'ID='.net::get('i'))->fetch();
              $channel = twitch::select(null, 'channels/'.ltrim(strtolower($channel['NAME']), '#'));
              if(isset($channel->error)) {
                net::fatal($channel->message);
              } else {
                $fields = array();
                if(isset($channel->logo)) {
                  $fields['LOGO'] = $channel->logo;
                }
                if(isset($channel->banner)) {
                  $fields['BANNER'] = $channel->banner;
                }
                $fields['UPDATEBY'] = auth::getUserName();
              
                $db->update('CHANNELS', $fields, 'ID='.net::get('i'));
                net::success('Sucessfully updated','refreshTable();');
              }
            } else {
              net::fatal('No ID '.$output);
            }
          break;
          case 'edit':
            if(net::get('i') != '') {
              $fields = array();
              $fields['NAME'] = net::post('name');
              $fields['LOGO'] = net::post('logo');
              $fields['BANNER'] = net::post('banner');
              $fields['ENABLED'] = intval(net::post('enabled'));
              $fields['UPDATEBY'] = auth::getUserName();

              $db->update('CHANNELS', $fields, 'ID='.net::get('i'));
              
              if(net::post('mute') == 1) {
                $config = array();
                $config['BOTID'] = auth::getBotId();
                $config['CHANNELID'] = net::get('i');
                $config['NAME'] = 'MUTE';
                $config['VALUE'] = 1;
                $fields['INSERTBY'] = auth::getUserName();
                $db->insert('CONFIG', $config);
              } else {
                $db->delete('CONFIG', 'BOTID='.auth::getBotId().' and CHANNELID='.net::get('i')." and NAME='MUTE'");
              }
              
              net::success('Sucessfully updated','refreshTable();');
            } else {
              net::fatal('No ID '.$output);
            }
          break;
          case 'del':
            if(net::get('i') != '') {
              $db->delete('CHANNELS', 'ID='.net::get('i'));
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
    }
  } else {
    die(net::noaccess('No Access'));
  }
  
?>