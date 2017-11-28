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
            $where = 'ID>0';
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
            $stmt = $db->select('BOTS', $where.$limit);
            $status = bot::execute('monitor', CACHE);
            $data = array();
            foreach($stmt->fetchAll() as $row) {
              if(isset($status->list)) {
                foreach($status->list as $state) {
                  if($state->id == $row['ID']) {
                    $row['STATE'] = $state->state;
                  }
                }
              }
              array_push($data, $row);
            }            
            net::data($db->query('SELECT COUNT(*) AS TOTAL FROM BOTS WHERE '.$where)->fetch()['TOTAL'], json_encode($data));
          break;
          case 'start':
            if(net::get('i') != '') {
              $status = bot::execute('start '.net::get('i'));
              net::success('Sucessfully started','refreshTable();');
            } else {
              net::fatal('No ID '.$output);
            }
          break;
          case 'stop':
            if(net::get('i') != '') {
              $status = bot::execute('stop '.net::get('i'));
              net::success('Sucessfully stopped','refreshTable();');
            } else {
              net::fatal('No ID '.$output);
            }
          break;
          case 'restart':
            if(net::get('i') != '') {
              $status = bot::execute('restart '.net::get('i'));
              net::success('Sucessfully restarted','refreshTable();');
            } else {
              net::fatal('No ID '.$output);
            }
          break;
          case 'enable':
            if(net::get('i') != '') {
              $db->update('BOTS', array('ENABLED' => 1, 'AUTOSTART' => 1, 'UPDATEBY' => auth::getUserName()), 'ID='.net::get('i'));
              $status = bot::execute('start '.net::get('i'));
              net::success('Sucessfully enabled','refreshTable();');
            } else {
              net::fatal('No ID '.$output);
            }
          break;
          case 'disable':
            if(net::get('i') != '') {
              $db->update('BOTS', array('ENABLED' => 0, 'AUTOSTART' => 0, 'UPDATEBY' => auth::getUserName()), 'ID='.net::get('i'));
              $status = bot::execute('stop '.net::get('i'));
              net::success('Sucessfully disabled','refreshTable();');
            } else {
              net::fatal('No ID '.$output);
            }
          break;
          case 'add':
            $user = $db->select('USERS', 'ID='.intval(net::post('id')))->fetch();
            
            $fields = array();
            $fields['COLOR'] = net::post('color');
            $fields['OWNER'] = net::post('owner');
            
            $fields['USERID'] = $user['ID'];
            $fields['NAME'] = $user['NAME'];
            $fields['LOGO'] = $user['LOGO'];
            $fields['BANNER'] = $user['BANNER'];
            $fields['OAUTH'] = $user['OAUTH'];
            $fields['AUTOSTART'] = 0;
            $fields['ENABLED'] = 1;
            $fields['INSERTBY'] = auth::getUserName();
          
            $db->insert('BOTS', $fields);
            net::success('Sucessfully added','refreshTable();');
          break;
          case 'update':
            if(net::get('i') != '') {
              $user = $db->select('USERS', 'ID='.net::get('ui'))->fetch();
              
              $fields = array();              
              $fields['NAME'] = $user['NAME'];
              $fields['LOGO'] = $user['LOGO'];
              $fields['BANNER'] = $user['BANNER'];
              $fields['OAUTH'] = $user['OAUTH'];
              $fields['UPDATEBY'] = auth::getUserName();
            
              $db->update('BOTS', $fields, 'ID='.net::get('i'));
              net::success('Sucessfully updated','refreshTable();');
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
              $fields['OAUTH'] = net::post('token');
              $fields['COLOR'] = net::post('color');
              $fields['AUTOSTART'] = intval(net::post('autostart'));
              $fields['ENABLED'] = intval(net::post('enabled'));
              $fields['OWNER'] = net::post('owner');
              $fields['UPDATEBY'] = auth::getUserName();
            
              $db->update('BOTS', $fields, 'ID='.net::get('i'));
              net::success('Sucessfully updated','refreshTable();');
            } else {
              net::fatal('No ID '.$output);
            }
          break;
          case 'del':
            if(net::get('i') != '') {
              $db->delete('BOTS', 'ID='.net::get('i'));
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