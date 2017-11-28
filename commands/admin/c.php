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
          $commands = array();
          foreach (scandir(CLI_BASE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$submodule->id) as $key => $value) { 
            if (!in_array($value,array(".",".."))) { 
              if(!in_array(str_replace('.php', '', $value), $commands)) {
                $row = array();
                $row['NAME'] = str_replace('.php', '', $value);
                try {
                  $execute = false;
                  $init = false;
                  include(CLI_BASE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$submodule->id.DIRECTORY_SEPARATOR.$value);
                } catch (Exception $e) {
                  $this->error($e);
                }
                $row['VALUE'] = $cmd['help'];
                $row['LEVEL'] = $cmd['level'];
                if(isset($cmd['syntax'])) {
                  $row['SYNTAX'] = htmlentities($cmd['syntax']);
                } else {
                  $row['SYNTAX'] = '';
                }
                array_push($commands, $row);
              }         
            }
          }
          
          sort($commands);
          net::data($db->query('SELECT COUNT(*) AS TOTAL FROM PLUGINS WHERE ID>0 and BOTID='.auth::getBotId().' and (CHANNELID=0 or CHANNELID='.auth::getChannelId().')')->fetch()['TOTAL'], json_encode($commands));
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