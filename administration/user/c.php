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
            $stmt = $db->select('USERS', $where.$limit);
            net::data($db->query('SELECT COUNT(*) AS TOTAL FROM USERS WHERE '.$where)->fetch()['TOTAL'], json_encode($stmt->fetchAll()));
          break;
          case 'enable':
            if(net::get('i') != '') {
              $db->update('USERS', array('ENABLED' => 1, 'UPDATEBY' => auth::getUserName()), 'ID='.net::get('i'));
              net::success('Sucessfully enabled','refreshTable();');
            } else {
              net::fatal('No ID '.$output);
            }
          break;
          case 'disable':
            if(net::get('i') != '') {
              $db->update('USERS', array('ENABLED' => 0, 'UPDATEBY' => auth::getUserName()), 'ID='.net::get('i'));
              net::success('Sucessfully disabled','refreshTable();');
            } else {
              net::fatal('No ID '.$output);
            }
          break;
          case 'edit':
            if(net::get('i') != '') {
              $fields = array();
              $fields['NAME'] = net::post('name');
              $fields['EMAIL'] = net::post('email');
              $fields['LOGO'] = net::post('logo');
              $fields['BANNER'] = net::post('banner');
              if(net::post('level') != '') {
                $fields['LEVEL'] = net::post('level');
              } else {
                $fields['LEVEL'] = null;
              }
              $fields['OAUTH'] = net::post('token');
              $fields['ENABLED'] = intval(net::post('enabled'));
              $fields['UPDATEBY'] = auth::getUserName();
            
              $db->update('USERS', $fields, 'ID='.net::get('i'));
              net::success('Sucessfully updated','refreshTable();');
            } else {
              net::fatal('No ID '.$output);
            }
          break;
          case 'del':
            if(net::get('i') != '') {
              $db->delete('USERS', 'ID='.net::get('i'));
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