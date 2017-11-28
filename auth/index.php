<?php

  require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');
  
  if(isset($_GET['logout'])) {
    auth::logout();
  } else {
    if(net::getCookie('USERHASH') != '' && AUTH_FORCE == false) {
      $access = twitch::validate(net::getCookie('USERHASH'));
      if(isset($access->token->valid) && $access->token->valid == true) {
        $userid = 0;
        $stmt = $db->select('USERS', "NAME='".strtolower($access->token->user_name)."' and ENABLED=1");
        if($stmt->rowCount() > 0) {
          $tmp = $stmt->fetch();
          auth::login($access->token->user_name, $tmp['ID'], 0, 0, $tmp['LEVEL']);
          header('Location: /');
          die(""); 
        } else {
          header('Location: '.AUTH.'?error=404&error_description=Invalid User');
          die("");
        }
      } else {
        net::removeCookie('USERHASH');
        header('Location: '.AUTH.'?error=401&error_description=Invalid Token');
        die("");
      }
    } else {
      if(isset($_GET['code'])) {
        $data = array(
          'client_id' => CLIENTID,
          'client_secret' => SECRET,
          'grant_type' => 'authorization_code',
          'redirect_uri' => REDIRECTURL,
          'code' => $_GET['code'],
          'state' => $_GET['state']
        );        
        $access = twitch::token($data);
        if(!isset($access->error)) {
          $user = twitch::select($access->access_token, 'user');
          $channel = twitch::select($access->access_token, 'channel');
          
          $stmt = $db->select('USERS', "NAME='".strtolower($user->display_name)."'");
          if($stmt->rowCount() > 0) {
            $tmp = $stmt->fetch();
            $db->update('USERS', array('OAUTH' => $access->access_token, 'EMAIL' => $user->email, 'SCOPE' => str_replace('+', ' ', AUTH_SCOPES), 'LOGO' => $user->logo, 'BANNER' => $channel->banner), 'ID='.$tmp['ID']);
            
            $stmt2 = $db->select('CHANNELS', "NAME='#".strtolower($tmp['NAME'])."'");
            if($stmt2->rowCount() == 1) {
              $channel = $stmt2->fetch();
              auth::login($tmp['NAME'], $tmp['ID'], $channel['BOTID'], $channel['ID'], $tmp['LEVEL']);
            } else {
              auth::login($tmp['NAME'], $tmp['ID'], 0, 0, $tmp['LEVEL']);
            }
          } else {
            if($db->select('USERS')->rowCount() <= 1) {
              $userid = $db->insert('USERS', array('NAME' => strtolower($user->display_name), 'EMAIL' => $user->email, 'SCOPE' => str_replace('+', ' ', AUTH_SCOPES), 'LEVEL' => auth::$owner, 'OAUTH' => $access->access_token, 'LOGO' => $user->logo, 'BANNER' => $channel->banner));
              
              $stmt2 = $db->select('CHANNELS', "NAME='#".strtolower($user->display_name)."'");
              if($stmt2->rowCount() == 1) {
                $channel = $stmt2->fetch();
                auth::login(strtolower($user->display_name), $userid, $channel['BOTID'], $channel['ID'], auth::$owner);
              } else {
                auth::login(strtolower($user->display_name), $userid, 0, 0, auth::$owner);
              }
            } else {
              $userid = $db->insert('USERS', array('NAME' => strtolower($user->display_name), 'EMAIL' => $user->email, 'SCOPE' => str_replace('+', ' ', AUTH_SCOPES), 'OAUTH' => $access->access_token, 'LOGO' => $user->logo, 'BANNER' => $channel->banner));
              $stmt2 = $db->select('CHANNELS', "NAME='#".strtolower($user->display_name)."'");
              if($stmt2->rowCount() == 1) {
                $channel = $stmt2->fetch();
                auth::login(strtolower($user->display_name), $userid, $channel['BOTID'], $channel['ID']);
              } else {
                auth::login(strtolower($user->display_name), $userid, 0, 0);
              }
            }
          }
          net::addCookie('USERHASH', $access->access_token);
          net::addCookie('USERNAME', strtolower($user->display_name));
          if(trim($user->logo) != '') {
            net::addCookie('USERLOGO', $user->logo);
          } else {
            net::removeCookie('USERLOGO');
          }
          header('Location: /');
          die("");      
        } else {
          header('Location: '.AUTH.'?error='.$access->status.'&error_description='.$access->message);
          die("");
        }
      } else {
        echo '<?xml version="1.0" encoding="ISO-8859-1" ?>';
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
        echo '<html xmlns="http://www.w3.org/1999/xhtml">';
        echo '<meta charset="utf-8">';
        echo '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
        echo '<head>';
        echo '<title>'.NAME.'</title>';  
        echo '<link rel="icon" type="image/x-icon" href="favicon.ico" />';
        echo '<meta name="robots" content="noindex">';
        
        $jsinclude = array(JS, INC);
        if($module != null) {
          array_push($jsinclude, BASE.DIRECTORY_SEPARATOR.$module->id);
        }
        foreach($jsinclude as $path) {
          foreach (scandir($path) as $include){
            if(is_file($path.DIRECTORY_SEPARATOR.$include) && strpos($include, '..') == 0 && strpos($include, 'min') == 0  && strtoupper(pathinfo($include, PATHINFO_EXTENSION)) == 'JS'){
              $ref = str_replace(DIRECTORY_SEPARATOR, URL_SEPARATOR, str_replace(BASE.DIRECTORY_SEPARATOR,'',$path)).URL_SEPARATOR.$include;
              if(COMPRESS && is_file($path.DIRECTORY_SEPARATOR.substr($include, 0, -3).'.min.'.substr($include,-2))) {
                $ref = str_replace(DIRECTORY_SEPARATOR, URL_SEPARATOR, str_replace(BASE.DIRECTORY_SEPARATOR,'',$path)).URL_SEPARATOR.substr($include, 0, -3).'.min.'.substr($include,-2);
              }          
              echo '<script type="text/javascript" src="'.URL_SEPARATOR.str_replace(DIRECTORY_SEPARATOR, URL_SEPARATOR, $ref).(CACHE ? '' : '?v='.time()).'"></script>';
            }
          }
        }
         
        $cssinclude = array(CSS, INC);
        if($module != null) {
          array_push($cssinclude, BASE.DIRECTORY_SEPARATOR.$module->id);
        }
        foreach($cssinclude as $path) {
          foreach (scandir($path) as $include){
            if(is_file($path.DIRECTORY_SEPARATOR.$include) && strpos($include, '..') == 0 && strpos($include, 'min') == 0  && strtoupper(pathinfo($include, PATHINFO_EXTENSION)) == 'CSS'){
              $ref = str_replace(BASE.DIRECTORY_SEPARATOR,'',$path).URL_SEPARATOR.$include;
              if(COMPRESS && is_file($path.DIRECTORY_SEPARATOR.substr($include, 0, -4).'.min.'.substr($include,-3))) {
                $ref = str_replace(DIRECTORY_SEPARATOR, URL_SEPARATOR, str_replace(BASE.DIRECTORY_SEPARATOR,'',$path)).URL_SEPARATOR.substr($include, 0, -4).'.min.'.substr($include,-3);
              }  
              echo '<link type="text/css" href="'.URL_SEPARATOR.str_replace(DIRECTORY_SEPARATOR, URL_SEPARATOR, $ref).(CACHE ? '' : '?v='.time()).'" rel="stylesheet" media="screen" />';
            }
          }
        }
        
        echo '</head>';
        echo '<body>';
        echo '<div class="container-fluid"><br><br>';
        echo '<div class="card-login card-container">';
        if(net::getCookie('USERLOGO') != '') {
          echo 'Last Login:<br><br><img id="profile-img" class="profile-img-card" src="'.net::getCookie('USERLOGO').'" />';
        } else {
          echo '<img id="profile-img" class="profile-img-card" src="avatar.png" />';
        }
        if(net::getCookie('USERNAME') != '') {
          echo '<p id="profile-name" class="profile-name-card">'.net::getCookie('USERNAME').'</p>';
        } else {
          echo '<p id="profile-name" class="profile-name-card"></p>';
        }
        echo '<br><form class="form-signin">';
        if(AUTH_FORCE == true) {
          echo '<a href="'.API.URL_SEPARATOR.'oauth2/authorize?response_type=code&force_verify=true&client_id='.urlencode(CLIENTID).'&redirect_uri='.urlencode(REDIRECTURL).'&scope='.AUTH_SCOPES.'&state='.time().'"><img src="connect_dark.png"/></a>';
        } else {
          echo '<a href="'.API.URL_SEPARATOR.'oauth2/authorize?response_type=code&client_id='.urlencode(CLIENTID).'&redirect_uri='.urlencode(REDIRECTURL).'&scope='.AUTH_SCOPES.'&state='.time().'"><img src="connect_dark.png"/></a>';
        }
        echo '</form>';
        if(isset($_GET['error'])) {
          echo '<br><br><div class="alert alert-danger" role="alert">'.$_GET['error_description'].'</div>';
        }
        echo '</div>';
        
        echo '</div>';
        echo '</body>';
        echo '</html>';
      }
    }
  }

?>