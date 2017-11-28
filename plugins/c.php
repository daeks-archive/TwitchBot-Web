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
          /*$plugins = array();
          foreach (scandir(CLI_BASE.DIRECTORY_SEPARATOR.'plugins') as $key => $value) { 
            if (!in_array($value,array(".",".."))) { 
              if(is_dir(CLI_BASE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$value)) {
                if(!in_array($value, $plugins)) {
                  array_push($plugins, $value);
                }
              } else {
                if(!in_array(str_replace('.php', '', $value), $plugins)) {
                  array_push($plugins, str_replace('.php', '', $value));
                }
              }           
            }
          }
          
          sort($plugins);*/
          
          $plugins = json_decode(file_get_contents(BASE.DIRECTORY_SEPARATOR.PLUGIN), true);
          usort($plugins, function($a, $b) { return strcmp($a["id"], $b["id"]); });
          
          $data = array();
          foreach($plugins as $plugin) {
            if(!isset($plugin['name'])) {
              $tmp = json_decode(file_get_contents(BASE.DIRECTORY_SEPARATOR.$plugin['id'].DIRECTORY_SEPARATOR.MODULE), true);
              $plugin['name'] = $tmp['name'];
              if(isset($tmp['menu'])) {
                $plugin['icon'] = $tmp['menu']['icon'];
              } else {
                $plugin['icon'] = '';
              }
            }
            $stmt = $db->select('PLUGINS', 'BOTID='.auth::getBotId().' and (CHANNELID=0 or CHANNELID='.auth::getChannelId().") and NAME='".$plugin['id']."' order by channelid desc");
            if($stmt->rowCount() > 0) {
              $row = $stmt->fetch();
              $row['CURRENTCHANNELID'] = auth::getChannelId();
              if(file_exists(BASE.DIRECTORY_SEPARATOR.$module->id.DIRECTORY_SEPARATOR.$plugin['id'].CONFIG_DIALOG)) {
                $row['CONFIG'] = 1;
              } else {
                $row['CONFIG'] = 0;
              }
              $row['DESCRIPTION'] = $plugin['name'];
              $row['ICON'] = $plugin['icon'];
              array_push($data, $row);
            } else {
              $row = array();
              $row['ID'] = $plugin['id'];
              $row['NAME'] = $plugin['name'];
              $row['DESCRIPTION'] = $plugin['name'];
              $row['ENABLED'] = 0;
              if(file_exists(BASE.DIRECTORY_SEPARATOR.$module->id.DIRECTORY_SEPARATOR.$plugin['id'].CONFIG_DIALOG)) {
                $row['CONFIG'] = 1;
              } else {
                $row['CONFIG'] = 0;
              }
              $row['ICON'] = $plugin['icon'];
              $row['CHANNELID'] = auth::getChannelId();
              $row['CURRENTCHANNELID'] = auth::getChannelId();
              array_push($data, $row);
            }              
          }
          net::data($db->query('SELECT COUNT(*) AS TOTAL FROM PLUGINS WHERE ID>0 and BOTID='.auth::getBotId().' and (CHANNELID=0 or CHANNELID='.auth::getChannelId().')')->fetch()['TOTAL'], json_encode($data));
        break;
        case 'enable':
          if(is_numeric(net::get('i'))) {
            if(net::get('i') != '') {
              $stmt = $db->select('PLUGINS', 'ID='.net::get('i'));
              if($stmt->rowCount() > 0) {
                $plugin = $stmt->fetch();
                $stmt2 = $db->select('PLUGINS', 'BOTID='.auth::getBotId().' and CHANNELID=0 and NAME="'.$plugin['NAME'].'"');
                if($stmt2->rowCount() > 0) {
                  $stmt3 = $db->select('CONFIG', 'BOTID='.auth::getBotId().' and CHANNELID='.auth::getChannelId().' and PLUGINID='.net::get('i'));
                  if($stmt3->rowCount() > 0) {
                    $db->update('PLUGINS', array('ENABLED' => 1, 'UPDATEBY' => auth::getUserName()), 'ID='.net::get('i'));
                  } else {
                    $db->delete('PLUGINS', 'ID='.net::get('i'));
                  }
                } else {
                  $db->update('PLUGINS', array('ENABLED' => 1, 'UPDATEBY' => auth::getUserName()), 'ID='.net::get('i'));
                }
                $status = bot::execute('reinit '.auth::getBotId().' '.auth::getChannelId());
                net::success('Sucessfully enabled','refreshTable();');
              } else {
                net::fatal('NOT FOUND '.$output);
              }
            } else {
              net::fatal('No ID '.$output);
            }
          } else {
            $fields = array();
            $fields['NAME'] = net::get('i');
            $fields['BOTID'] = auth::getBotId();
            $fields['CHANNELID'] = auth::getChannelId();
            $fields['INSERTBY'] = auth::getUserName();
            
            $db->insert('PLUGINS', $fields);
            $status = bot::execute('reinit '.auth::getBotId().' '.auth::getChannelId());
            net::success('Sucessfully enabled','refreshTable();');
          }
        break;
        case 'disable':
          if(is_numeric(net::get('i'))) {
            if(net::get('i') != '') {
              $stmt3 = $db->select('CONFIG', 'BOTID='.auth::getBotId().' and CHANNELID='.auth::getChannelId().' and PLUGINID='.net::get('i'));
              if($stmt3->rowCount() > 0) {
                $db->update('PLUGINS', array('ENABLED' => 0, 'UPDATEBY' => auth::getUserName()), 'ID='.net::get('i'));
              } else {
                $db->delete('PLUGINS', 'ID='.net::get('i'));
              }
              $status = bot::execute('reinit '.auth::getBotId().' '.auth::getChannelId());
              net::success('Sucessfully disabled','refreshTable();');
            } else {
              net::fatal('No ID '.$output);
            }
          } else {
            $fields = array();
            $fields['NAME'] = net::get('i');
            $fields['ENABLED'] = 0;
            $fields['BOTID'] = auth::getBotId();
            $fields['CHANNELID'] = auth::getChannelId();
            $fields['INSERTBY'] = auth::getUserName();
            
            $db->insert('PLUGINS', $fields);
            $status = bot::execute('reinit '.auth::getBotId().' '.auth::getChannelId());
            net::success('Sucessfully disabled','refreshTable();');
          }
        break;
        case 'del':
          if(net::get('i') != '') {
            $db->delete('PLUGINS', 'ID='.net::get('i'));
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