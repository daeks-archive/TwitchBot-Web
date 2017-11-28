<?php

	class core {
	
		public static $time;
	
		public static function displayHeader($js = null, $cache = CACHE) {
			self::$time = microtime(true);
			echo '<?xml version="1.0" encoding="ISO-8859-1" ?>';
			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
			echo '<html xmlns="http://www.w3.org/1999/xhtml">';
			echo '<meta charset="utf-8">';
      echo '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
      echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
			echo '<head>';
			
			$module = utils::config();
			$submodule = utils::subconfig();
			if($module != null) {
        $modulename = $module->name;
        $modulepath = BASE.DIRECTORY_SEPARATOR.$module->id;
        if(isset($submodule->id)) {
          if(file_exists(BASE.DIRECTORY_SEPARATOR.$module->id.DIRECTORY_SEPARATOR.$submodule->id)) {
            $modulename = $submodule->name;
            $modulepath = BASE.DIRECTORY_SEPARATOR.$module->id.DIRECTORY_SEPARATOR.$submodule->id;
          }
        }
			} else {
        $modulename = null;
        $modulepath = null;
			}

			if($modulename != null) {
        echo '<title>'.NAME.' - '.$modulename.'</title>';
			} else {
        echo '<title>'.NAME.'</title>';
			}
			
			echo '<link rel="icon" type="image/x-icon" href="../../'.FAVICON.'" />';
			echo '<meta name="robots" content="noindex">';
      
      $jsinclude = array(JS, INC);
      if($modulepath != null) {
        array_push($jsinclude, $modulepath);
			}
			foreach($jsinclude as $path) {
        foreach (scandir($path) as $include){
          if(is_file($path.DIRECTORY_SEPARATOR.$include) && strpos($include, '..') == 0 && strpos($include, 'min') == 0  && strtoupper(pathinfo($include, PATHINFO_EXTENSION)) == 'JS'){
            $ref = str_replace(DIRECTORY_SEPARATOR, URL_SEPARATOR, str_replace(BASE.DIRECTORY_SEPARATOR,'',$path)).URL_SEPARATOR.$include;
            if(COMPRESS && is_file($path.DIRECTORY_SEPARATOR.substr($include, 0, -3).'.min.'.substr($include,-2))) {
              $ref = str_replace(DIRECTORY_SEPARATOR, URL_SEPARATOR, str_replace(BASE.DIRECTORY_SEPARATOR,'',$path)).URL_SEPARATOR.substr($include, 0, -3).'.min.'.substr($include,-2);
            }          
            echo '<script type="text/javascript" src="'.URL_SEPARATOR.str_replace(DIRECTORY_SEPARATOR, URL_SEPARATOR, $ref).($cache ? '' : '?v='.time()).'"></script>';
          }
        }
			}
			
			$cssinclude = array(CSS, INC);
      if($modulepath != null) {
        array_push($cssinclude, $modulepath);
			}
			foreach($cssinclude as $path) {
        foreach (scandir($path) as $include){
          if(is_file($path.DIRECTORY_SEPARATOR.$include) && strpos($include, '..') == 0 && strpos($include, 'min') == 0  && strtoupper(pathinfo($include, PATHINFO_EXTENSION)) == 'CSS'){
            $ref = str_replace(BASE.DIRECTORY_SEPARATOR,'',$path).URL_SEPARATOR.$include;
            if(COMPRESS && is_file($path.DIRECTORY_SEPARATOR.substr($include, 0, -4).'.min.'.substr($include,-3))) {
              $ref = str_replace(DIRECTORY_SEPARATOR, URL_SEPARATOR, str_replace(BASE.DIRECTORY_SEPARATOR,'',$path)).URL_SEPARATOR.substr($include, 0, -4).'.min.'.substr($include,-3);
            }  
            echo '<link type="text/css" href="'.URL_SEPARATOR.str_replace(DIRECTORY_SEPARATOR, URL_SEPARATOR, $ref).($cache ? '' : '?v='.time()).'" rel="stylesheet" media="screen" />';
          }
        }
			}
					
			echo '</head>';
			echo '<body '.(isset($js)?'onload="'.$js.'"':'').'>';
			echo '<div class="container-fluid">';
      echo '<div class="row">';
		}
		
		public static function addCSS($ref, $cache = false) {
			return '<link type="text/css" href="'.str_replace(DIRECTORY_SEPARATOR, URL_SEPARATOR, $ref).($cache ? '' : '?v='.time().'"').' rel="stylesheet" media="screen" />';
		}
		
		public static function addJS($ref, $cache = false) {
			return '<script type="text/javascript" src="'.str_replace(DIRECTORY_SEPARATOR, URL_SEPARATOR, $ref).($cache ? '' : '?v='.time().'"').'></script>';
		}
		
		public static function getModules() {
      $tmp = array();
      foreach (scandir(BASE) as $include){
				if(is_dir(BASE.DIRECTORY_SEPARATOR.$include) && is_file(BASE.DIRECTORY_SEPARATOR.$include.DIRECTORY_SEPARATOR.MODULE)){
          array_push($tmp, BASE.DIRECTORY_SEPARATOR.$include.DIRECTORY_SEPARATOR.MODULE);
				}
			}
			return $tmp;
		}
		
		public static function startPage() {
      $access = true;
      
      if(auth::hasAccess()) {
        $db = new db();
        $stmt = $db->select('CHANNELS', "NAME='#".strtolower(auth::getUserName())."'");
        if($stmt->rowCount() == 0) {
          if(!auth::checkAccess(auth::$owner)) {
            $access = false;
          }
        } else {
          if(!auth::checkAccess(auth::$owner) && auth::getBotId() == 0) {
            $access = false;
          }
        }
      } 
            
      if($access) {
        $parent = utils::config();
        $module = utils::subconfig();
        $modules = self::getModules();
        $menu = array();
        $plugins = json_decode(file_get_contents(BASE.DIRECTORY_SEPARATOR.PLUGIN), true);
        
        foreach ($modules as $moduleconfig) {
          $tmp = json_decode(file_get_contents($moduleconfig));
          $item = "";
          if(isset($tmp->menu)) {
            if(isset($tmp->level) && !auth::checkAccess($tmp->level)) {
              if($parent != null && $tmp->id == $parent->id || $module->id == $tmp->id) {
                $access = false;
              }
              continue;
            }
            if(isset($tmp->menu->submenu)) {
              $noaccess = 0;
              foreach($tmp->menu->submenu as $subtmp) {
                if(isset($subtmp->level) && !auth::checkAccess($subtmp->level)) {
                  $noaccess++;
                }
              }
              if(sizeof($tmp->menu->submenu) == $noaccess) {
                continue;
              }
              if($module != null && $tmp->id == $module->id && !isset($parent->id)) {
                $item .= '<li class="active">';
              } else {
                if(isset($tmp->enabled) && !$tmp->enabled) {
                  $item .= '<li class="disabled">';
                } else {
                  $item .= '<li>';
                }
              }
              $item .= '<a data-toggle="collapse" data-target="#'.$tmp->id.'" href="#">';
              if($tmp->menu->icon != '') {
                $item .= '<i class="fa fa-'.$tmp->menu->icon.' fa-fw"></i> ';
              }
              $item .= $tmp->name;
              $item .= ' <b class="dropdown-caret glyphicon glyphicon-chevron-down pull-right"></b>';
              if(isset($tmp->level)) {
                $item .= '<i class="fa fa-lock fa-fw pull-right" data-title="tooltip" data-placement="left" title="Locked"></i>';
              }
              $item .= '</a>';
              if(isset($tmp->menu->collapse) && $tmp->menu->collapse == 0 || $parent != null && $tmp->id == $parent->id) {
                $item .= '<form id="'.$tmp->id.'" class="dropdown-menu dropdown-icon collapse-icon collapse in">';
              } else {
                $item .= '<form id="'.$tmp->id.'" class="dropdown-menu dropdown-icon collapse-icon collapse">';
              }
              
              $submenu = array();
              foreach($tmp->menu->submenu as $subtmp) {
                $subitem = "";
                if(isset($subtmp->level) && !auth::checkAccess($subtmp->level)) {
                  if($module->id == $subtmp->id) {
                    $access = false;
                  }
                  continue;
                }
                if($subtmp->id == '-') {
                  $subitem .= '<li class="divider"></li>';
                } else if($subtmp->id == 'header') {
                  $subitem .= '<li class="dropdown-header">'.$subtmp->name.'</li>';
                } else {
                  if($module != null && $subtmp->id == $module->id) {
                    $subitem .= '<li class="active">';
                  } else {
                    if(isset($subtmp->enabled) && !$subtmp->enabled) {
                      $subitem .= '<li class="disabled">';
                    } else {
                      $subitem .= '<li>';
                    }
                  }
                  if($subtmp->id != '') {
                    $subitem .= '<a href="'.URL_SEPARATOR.$tmp->id.URL_SEPARATOR.$subtmp->id.URL_SEPARATOR.'">';
                  } else {
                    $subitem .= '<a href="'.URL_SEPARATOR.$tmp->id.URL_SEPARATOR.'">';
                  }
                  if(isset($subtmp->icon) && $subtmp->icon != '') {
                    $subitem .= '<i class="fa fa-'.$subtmp->icon.' fa-fw"></i> ';
                  }
                  $subitem .= $subtmp->name;
                  if(isset($subtmp->badge) && $subtmp->badge != '') {
                    $db = new db();
                    $stmt = $db->query($subtmp->badge);
                    if($stmt->rowCount() > 0) {
                      $subitem .= ' <span class="badge pull-right">'.$stmt->rowCount().'</span>';
                    }
                    $db = null;
                  }
                  foreach($plugins as $plugin) {
                    if($plugin['id'] == $subtmp->id) {
                      $stmt = $db->select('PLUGINS', "BOTID=".auth::getBotId()." and (CHANNELID=".auth::getChannelId()." or CHANNELID=0) and NAME='".$subtmp->id."' and enabled=1 order by botid desc, channelid desc");
                      if($stmt->rowCount() == 0) {
                        $subitem .= '<i class="fa fa-ban fa-fw pull-right" data-title="tooltip" data-placement="left" title="Disabled"></i>';
                      }
                      break;
                    }
                  }
                  if(isset($subtmp->level)) {
                    $subitem .= '<i class="fa fa-lock fa-fw pull-right" data-title="tooltip" data-placement="left" title="Locked"></i>';
                  }
                  if(isset($subtmp->beta) && $subtmp->beta == 1) {
                    $item .= '<i class="fa fa-flask fa-fw pull-right" data-title="tooltip" data-placement="left" title="BETA"></i>';
                  }
                  $subitem .= '</a></li>';
                }
                if (isset($subtmp->order)) {
                  if(!array_key_exists($subtmp->order,$submenu)) {
                    $submenu[$subtmp->order] = $subitem;
                  } else {
                    $obj = $submenu[$subtmp->order];
                    $submenu[$subtmp->order] = $subitem;
                    for($i=0;$i<1000;$i++) {
                      if(!isset($submenu[$i])) {
                        $submenu[$i] = $obj;
                        break;
                      }
                    }
                  }
                } else {
                  for($i=0;$i<1000;$i++) {
                    if(!isset($submenu[$i])) {
                      $submenu[$i] = $subitem;
                      break;
                    }
                  }
                }
              }
              ksort($submenu);
              foreach($submenu as $subitem) {
                $item .= $subitem;
              }
              $item .= '</form></li>';
            } else {
              if($module != null && $tmp->id == $module->id) {
                $item .= '<li class="active">';
              } else {
                if(isset($tmp->enabled) && !$tmp->enabled) {
                  $item .= '<li class="disabled">';
                } else {
                  $item .= '<li>';
                }
              }
              $item .= '<a href="'.URL_SEPARATOR.$tmp->id.URL_SEPARATOR.'">';
              if(isset($tmp->menu->icon) && $tmp->menu->icon != '') {
                $item .= '<i class="fa fa-'.$tmp->menu->icon.' fa-fw"></i> ';
              }
              $item .= $tmp->name;
              if(isset($tmp->menu->badge) && $tmp->menu->badge != '') {
                $db = new db();
                $stmt = $db->query($tmp->menu->badge);
                if($stmt->rowCount() > 0) {
                  $item .= ' <span class="badge pull-right">'.$stmt->rowCount().'</span>';
                }
                $db = null;
              }
              foreach($plugins as $plugin) {
                if($plugin['id'] == $tmp->id) {
                  $stmt = $db->select('PLUGINS', "BOTID=".auth::getBotId()." and (CHANNELID=".auth::getChannelId()." or CHANNELID=0) and NAME='".$tmp->id."' and enabled=1 order by botid desc, channelid desc");
                  if($stmt->rowCount() == 0) {
                    $item .= '<i class="fa fa-ban fa-fw pull-right" data-title="tooltip" data-placement="left" title="Disabled"></i>';
                  }
                  break;
                }
              }
              if(isset($tmp->level)) {
                $item .= '<i class="fa fa-lock fa-fw pull-right" data-title="tooltip" data-placement="left" title="Locked"></i>';
              }
              if(isset($tmp->beta) && $tmp->beta == 1) {
                $item .= '<i class="fa fa-flask fa-fw pull-right" data-title="tooltip" data-placement="left" title="BETA"></i>';
              }
              $item .= '</a></li>';
            }
            
            if (isset($tmp->menu->order)) {
              if(!array_key_exists($tmp->menu->order,$menu)) {
                $menu[$tmp->menu->order] = $item;
              } else {
                $obj = $menu[$tmp->menu->order];
                $menu[$tmp->menu->order] = $item;
                for($i=0;$i<1000;$i++) {
                  if(!isset($menu[$i])) {
                    $menu[$i] = $obj;
                    break;
                  }
                }
              }
            } else {
              for($i=0;$i<1000;$i++) {
                if(!isset($menu[$i])) {
                  $menu[$i] = $item;
                  break;
                }
              }
            }
          }
        }
      }
      
      if($access) {
        echo '<div class="col-md-2 col-lg-2">';
        echo '<nav class="navbar navbar-default navbar-fixed-side display-xs display-sm display-md display-lg">';
        echo '<div class="container pull-sm-left">';
        echo '<div class="navbar-header">';
        echo '<a class="navbar-brand" href="'.URL_SEPARATOR.'"><span><img alt="Brand" src="../../'.BRAND.'" class="brand-logo"></span> '.NAME.'</a>';
        echo '</div>';
        echo '<div class="collapse navbar-collapse hidden-xs hidden-sm display-md display-lg">';
        echo '<ul class="nav navbar-nav hidden-xs hidden-sm display-md display-lg">';
        ksort($menu);
        foreach($menu as $item) {
          echo $item;
        }
        //echo '<li><br><a href="../../auth?logout=true"><span class="fa navbar-fa fa-sign-out" aria-hidden="true"></span> Logout as '.$_SESSION['USERNAME'].'</a></li>';
        echo '</ul>';
        echo '<p class="text-muted copyright hidden-xs hidden-sm display-md display-lg"> <i id="loading" class="fa fa-spinner fa-spin hidden"></i> (c) '.date('Y',time()).' '.NAME.' - '.number_format(microtime(true) - self::$time, 3).'s</p>';
        echo '</div>';
        echo '</div>';
        echo '</nav>';
        echo '</div>';
        echo '<div class="modal" id="modal" tabindex="-1" role="dialog"><div class="modal-dialog"><div class="modal-content" id="modal-content"></div></div></div>';
      } else {
        echo '<div class="col-sm-12 col-lg-12">';
        echo '<nav class="navbar navbar-default navbar-fixed-top">';
        echo '<div class="container">';
        echo '<div class="navbar-left navbar-header">';
        echo '<a class="navbar-brand" href="'.URL_SEPARATOR.'"><span><img alt="Brand" src="../../'.BRAND.'" class="brand-logo"></span> '.NAME.'</a>';
        echo '</div>';
        echo '<div class="navbar-right display-xs display-sm display-md display-lg" style="position: absolute !important;right: 15px !important;">';
        echo '<ul class="nav navbar-nav">';
        echo '<li><a href="../../auth?logout=true"><span class="fa navbar-fa fa-sign-out" aria-hidden="true"></span> Logout as '.auth::getUserName().'</a></li>';
        echo '</ul>';
        echo '</div>';
        echo '</div>';
        echo '</nav>';
        echo '<div class="display-xs hidden-sm hidden-md hidden-lg"><br><br><br><br>';
        echo '<div class="alert alert-danger"><b>No Access</b><br>Mobile devices are currently not supported.</div>';
        echo '</div>';
        echo '<div class="hidden-xs display-sm hidden-md hidden-lg">';
        echo '<br><br><br><br>';
        echo '<div class="alert alert-danger"><b>No Access</b><br>Tablet devices are currently not supported.</div>';
        echo '</div>';
        echo '<div class="hidden-xs hidden-sm display-md display-lg">';
        echo '<br><br><br><br>';
        echo '<div class="alert alert-danger"><b>No Access</b><br>Sorry, you are not permitted to use this page.</div>';
        echo '</div>';
        echo '</div>';
        echo '</body>';
        echo '</html>';
        die("");
      }
		}
				
		public static function endPage($spacer = '') {
      echo '<div class="display-xs hidden-sm hidden-md hidden-lg not-supported">';
      echo $spacer;
      echo '<div class="alert alert-danger"><b>Mobile devices are currently not supported.</b><br>Please use a larger screen environment.</div>';
      echo '</div>';
      echo '<div class="hidden-xs display-sm hidden-md hidden-lg not-supported">';
      echo $spacer;
      echo '<div class="alert alert-danger"><b>Tablet devices are currently not supported.</b><br>Please use a larger screen environment.</div>';
      echo '</div>';
      echo '</div>';
			echo '</body>';
			echo '</html>';
		}
		
		public static function startModule($botid = -1, $channelid = -1, $infobox = '') {
      if($botid == -1) {
        $botid = auth::getBotId();
      }
      if($channelid == -1) {
        $channelid = auth::getChannelId();
      }
      $parent = utils::config();
      $module = utils::subconfig();
      echo '<div class="col-sm-10 col-lg-10 hidden-xs hidden-sm display-md display-lg">';
      echo '<div class="userbar">';
      $db = new db();
      if(auth::checkAccess(auth::$owner)) {
        $stmt = $db->select('BOTS');
        if($stmt->rowCount() > 0) {
          echo '<div class="module-select pull-left">';
          echo '<select data-fv-notempty class="form-control select" data-toggle="async" data-query="/core/c.php?a=bot&i=">';
          foreach($stmt->fetchAll() as $bot) {
            echo '<option value="'.$bot['ID'].'" ';
            if($botid == $bot['ID']) {
              echo 'selected';
            }
            echo '>'.$bot['NAME'].'</option>';
          }
          echo '</select>';
          echo '</div>';
        }
      }
      $bot = $db->select('BOTS', 'ID='.$botid)->fetch();
      if($botid > 0) {
        $status = bot::execute('status '.$bot['ID']);
        if(isset($status->state)) {
          if($status->state == 'RUNNING') {
            echo ' <div class="module-icon pull-left grad-success" data-title="tooltip" data-placement="bottom" title="'.$status->state.'"></div>';
          } else if($status->state == 'STOPPED') {
            echo ' <div class="module-icon pull-left grad-danger" data-title="tooltip" data-placement="bottom" title="'.$status->state.'"></div>';
          } else {
            echo ' <div class="module-icon pull-left grad-invalid" data-title="tooltip" data-placement="bottom" title="'.$status->state.'"></div>';
          }
        } else {
          echo ' <div class="module-icon pull-left grad-invalid" data-title="tooltip" data-placement="bottom" title="FAILED"></div>';
        }
      }
      echo '<h3 class="module-name pull-left">'.$bot['NAME'].'</h3>';
      if(auth::checkAccess(auth::$owner)) {
        $db = new db();
        $stmt = $db->select('CHANNELS', 'BOTID='.$botid);
        if($stmt->rowCount() > 0) {
          echo '<div class="module-select pull-left">';
          echo '<select data-fv-notempty class="form-control select" data-toggle="async" data-query="/core/c.php?a=channel&i=">';
          echo '<option value="0"';
          if($channelid == 0) {
            echo ' selected';
          }
          echo '>GLOBAL</option>';
          foreach($stmt->fetchAll() as $channel) {
            echo '<option value="'.$channel['ID'].'" ';
            if($channelid == $channel['ID']) {
              echo 'selected';
            }
            echo '>'.$channel['NAME'].'</option>';
          }
          echo '</select>';
          echo '</div>';
        } else {
          echo '<div class="module-select pull-left">';
          echo '<select data-fv-notempty class="form-control select" disabled>';
          echo '<option value="0">GLOBAL</option>';
          echo '</select>';
          echo '</div>';
        }
      }
      if($channelid > 0) {
        $channel = $db->select('CHANNELS', 'ID='.$channelid.' and BOTID='.$botid)->fetch();
        echo '<h4 class="module-subname pull-left">-  '.strtoupper(ltrim($channel['NAME'], '#')).'</h4>';
      }
      echo '<div class="user-img-box pull-right dropdown">';
      echo '<a class="dropdown-toggle" data-toggle="dropdown" href="#">';
      if(net::getCookie('USERLOGO') != '') {
        echo '<img id="profile-img" class="user-img" src="'.net::getCookie('USERLOGO').'" />';
      } else {
        echo '<img id="profile-img" class="user-img" src="../../auth/avatar.png" />';
      }
      echo '</a>';
      echo '<ul class="dropdown-menu" aria-labelledby="usermenu">';
      if(auth::getUsername() != null) {
        echo '<li class="user-name"><a href="http://twitch.tv/'.auth::getUsername().'" target="_blank"><span class="fa navbar-fa fa-twitch" aria-hidden="true"></span> '.auth::getUsername().'</a></li>';
        echo '<li role="separator" class="divider"></li>';
        echo '<li><a href="../../auth?logout=true"><span class="fa navbar-fa fa-sign-out" aria-hidden="true"></span> Logout</a></li>';
      } else {
        echo '<li><a href="../../auth"><span class="fa navbar-fa fa-sign-in" aria-hidden="true"></span> Login</a></li>';
      }
      echo '</ul>';
      echo '</div>';
      if(isset($module->external) && auth::getBotId() != 0) {
        echo '<ul class="nav navbar-default navbar-nav pull-right userbar-default">';
        $db = new db();
        foreach($module->external as $external) {
          if(isset($external->enabled) && !$external->enabled) {
            continue;
          }
          $bot = $db->select('BOTS', 'ID='.auth::getBotId())->fetch();
          if($channelid > 0) {
            $channel = $db->select('CHANNELS', 'ID='.$channelid.' and BOTID='.$botid)->fetch();
            echo '<li><a href="/'.$module->id.URL_SEPARATOR.$external->id.'?'.strtolower($bot['NAME']).URL_SEPARATOR.strtolower(ltrim($channel['NAME'], '#')).'" target="_blank">';
          } else {
            $stmt = $db->select('CHANNELS', 'BOTID='.auth::getBotId());
            if($stmt->rowCount() == 1) {
              $channel = $stmt->fetch();
              echo '<li><a href="/'.$module->id.URL_SEPARATOR.$external->id.'?'.strtolower($bot['NAME']).URL_SEPARATOR.strtolower(ltrim($channel['NAME'], '#')).'" target="_blank">';
            } else {
              echo '<li><a href="/'.$module->id.URL_SEPARATOR.$external->id.'?'.strtolower($bot['NAME']).'" target="_blank">';
            }
          }
          if(isset($external->icon) && $external->icon != '') {
            echo '<span class="fa navbar-fa fa-'.$external->icon.'"></span> ';
          }
          echo $external->name.'</a></li>';
        }
        echo '</ul>';
      }
      echo '</div>';
      echo '<div id="userbar" class="userbar-spacer"></div>';
      echo '<div id="content" class="content">';
      echo '<div id="infobox" class="infobox">'.$infobox.'</div>';
      $cli = bot::checkConnection();
      if($cli->state != 'OK') {
        echo '<div id="infobox" class="infobox"><div class="alert alert-warning" tabindex="-1">';
        echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
        echo '<span><strong>Connection Error!</strong> Unable to connect to Bot Framework on '.CLI_SERVER.':'.CLI_PORT.'</span>';
        echo '</div></div>';
      }
      if($botid > 0) {
        $plugins = json_decode(file_get_contents(BASE.DIRECTORY_SEPARATOR.PLUGIN), true);
        foreach($plugins as $plugin) {
          if($plugin['id'] == $module->id) {
            $stmt = $db->select('PLUGINS', "BOTID=".$botid." and (CHANNELID=".$channelid." or CHANNELID=0) and NAME='".$module->id."' and enabled=1 order by botid desc, channelid desc");
            if($stmt->rowCount() == 0) {
              echo '<div class="not-supported">';
              echo '<div class="alert alert-danger"><b>Plugin '.$module->id.' disabled</b><br>This plugin is not enabled for this channel';
              echo '<br><br><button class="btn btn-success" data-toggle="modal" href="../../plugins/d.php?a=enable&i='.$module->id.'" data-target="#modal">Enable Plugin</button>';
              echo '</div>';
              die(core::endModule());
            }
            break;
          }
        }
      } 
      echo '<div id="topbar">';
      echo '<h3>';
      if(isset($module->beta) && $module->beta == 1) {
        echo '<i class="fa fa-flask fa-fw" data-title="tooltip" data-placement="bottom" title="BETA"></i> ';
      }
      echo $module->name.'</h3>';
      echo '<ol class="breadcrumb" style="margin-bottom: 5px;">';
      echo '<li><a href="'.URL_SEPARATOR.'">Home</a></li>';
      if(isset($parent->id) && $parent->id != $module->id) {
        echo '<li><a href="'.URL_SEPARATOR.$parent->id.'">'.$parent->name.'</a></li>';
      }
      echo '<li class="active">'.$module->name.'</li>';
      echo '</ol>';
      echo '</div>';
      if($botid > 0 && $channelid > 0) {
        $stmt = $db->select('CONFIG', 'BOTID='.$botid.' and CHANNELID='.$channelid." and NAME='MUTE'");
        if($stmt->rowCount() > 0) {
          echo '<div id="infobox" class="infobox"><div class="alert alert-info" tabindex="-1">';
          echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
          echo '<span><strong>Info</strong> Channel is muted!</span>';
          echo '</div></div>';
        }
      }   
		}
		
		public static function endModule($reset = false) {
      if($reset == true) {
        echo '<br><div class="pull-right"><button class="btn btn-link" data-toggle="modal" href="d.php?a=reset" data-target="#modal"> <i class="fa fa-trash"></i> Reset</button></div>';
      }
      echo '</div>';
      echo '</div>';
		}
		
		public static function startExternalPage($bot = null, $channel = null) {
      $access = true;
      $parent = utils::config();
      $module = utils::subconfig();
      
      $modules = self::getModules();
      $menu = array();
      foreach ($modules as $moduleconfig) {
        $tmp = json_decode(file_get_contents($moduleconfig));
        $item = "";
        if(isset($tmp->external)) {
          foreach($tmp->external as $subtmp) {
            if(isset($subtmp->enabled) && !$subtmp->enabled) {
              if($module != null && $subtmp->id == $module->id && $tmp->id == $parent->id) {
                $access = false;
              }
            }
            if(isset($subtmp->hidden) && $subtmp->hidden) {
              continue;
            }
            if($module != null && $subtmp->id == $module->id && $tmp->id == $parent->id) {
              $item .= '<li class="active">';
              $item .= '<a href="'.URL_SEPARATOR.$tmp->id.URL_SEPARATOR.$subtmp->id.URL_SEPARATOR;
              if($bot != null) {
                $item .= '?'.strtolower($bot['NAME']);
                if($channel != null) {
                  $item .= URL_SEPARATOR.ltrim(strtolower($channel['NAME']), '#');
                } else {
                  $db = new db();
                  $stmt = $db->select('CHANNELS', 'BOTID='.$bot['ID']);
                  if($stmt->rowCount() == 1) {
                    $channel = $stmt->fetch();
                    $item .= URL_SEPARATOR.ltrim(strtolower($channel['NAME']), '#');
                  }
                }
              }
            } else {
              if(isset($subtmp->enabled) && !$subtmp->enabled) {
                $item .= '<li class="disabled">';
                $item .= '<a href="#';
              } else {
                $item .= '<li>';
                $item .= '<a href="'.URL_SEPARATOR.$tmp->id.URL_SEPARATOR.$subtmp->id.URL_SEPARATOR;
                if($bot != null) {
                  $item .= '?'.strtolower($bot['NAME']);
                  if($channel != null) {
                    $item .= URL_SEPARATOR.ltrim(strtolower($channel['NAME']), '#');
                  } else {
                    $db = new db();
                    $stmt = $db->select('CHANNELS', 'BOTID='.$bot['ID']);
                    if($stmt->rowCount() == 1) {
                      $channel = $stmt->fetch();
                      $item .= URL_SEPARATOR.ltrim(strtolower($channel['NAME']), '#');
                    }
                  }
                }
              }
            }
            $item .= '">';
            if(isset($subtmp->icon) && $subtmp->icon != '') {
              $item .= '<i class="fa fa-'.$subtmp->icon.' fa-fw"></i> ';
            }
            $item .= $subtmp->name;
            if(isset($subtmp->badge) && $subtmp->badge != '') {
              $db = new db();
              $stmt = $db->query($subtmp->badge);
              if($stmt->rowCount() > 0) {
                $item .= ' <span class="badge pull-right">'.$stmt->rowCount().'</span>';
              }
              $db = null;
            }
            $item .= '</a></li>';
            
            if (isset($subtmp->order)) {
              if(!array_key_exists($subtmp->order,$menu)) {
                $menu[$subtmp->order] = $item;
              } else {
                $obj = $menu[$subtmp->order];
                $menu[$subtmp->order] = $item;
                for($i=0;$i<1000;$i++) {
                  if(!isset($menu[$i])) {
                    $menu[$i] = $obj;
                    break;
                  }
                }
              }
            } else {
              for($i=0;$i<1000;$i++) {
                if(!isset($menu[$i])) {
                  $menu[$i] = $item;
                  break;
                }
              }
            }
            
          }
        }
      }
      echo '<nav class="navbar navbar-inverse navbar-static-top display-xs display-sm display-md display-lg">';
      if($access) {
        echo '<div class="container pull-sm-left">';
      } else {
        echo '<div class="container pull-sm-left">';
      }
      
      echo '<div class="navbar-header">';
      if($bot['LOGO'] != null && $bot['LOGO'] != '') {
        ///dashboard/view/?'.strtolower($bot['NAME']).'
        echo '<a class="navbar-brand" href="#"><span><img alt="Brand" src="'.$bot['LOGO'].'" class="brand-logo"></span> <b>'.$bot['NAME'].'</b></a>';
      } else {
        echo '<a class="navbar-brand" href="#"><span><img alt="Brand" src="../../core/img/avatar.png" class="brand-logo"></span> <b>'.$bot['NAME'].'</b></a>';
      }
      echo '</div>';
      
      echo '<div id="navbar" class="navbar-collapse collapse">';
      
      echo '<ul class="nav navbar-nav hidden-xs hidden-sm display-md display-lg">';
      ksort($menu);
      foreach($menu as $item) {
        echo $item;
      }
      echo '</ul>';

      if(isset($module->search)) {      
        echo '<div class="nav navbar-nav pull-right hidden-xs hidden-sm display-md display-lg"">';
        echo '<form class="navbar-form" role="search">';
        echo '<div class="input-group">';
        echo '<input type="text" class="form-control search"';
        if(!$module->search->enabled) {
          echo ' disabled';
        }
        echo ' placeholder="'.$module->search->name.'" data-query="'.$module->search->query.'?'.strtolower($bot['NAME']);
        if($channel != null) {
          echo URL_SEPARATOR.ltrim(strtolower($channel['NAME']), '#');
        }
        echo '" data-type="'.$module->search->type.'" data-target="'.$module->search->target.'">';
        echo '<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>';
        echo '</div>';
        echo '</form>';
        echo '</div>';
      }
                    
      echo '</div>';
      
      echo '</div>';
      echo '</nav>';
      
      if(!$access) {
        echo '<div class="container-fluid">';
        echo '<div class="display-xs hidden-sm hidden-md hidden-lg">';
        echo '<div class="alert alert-danger"><b>No Access</b><br>Mobile devices are currently not supported.</div>';
        echo '</div>';
        echo '<div class="hidden-xs display-sm hidden-md hidden-lg">';
        echo '<div class="alert alert-danger"><b>No Access</b><br>Tablet devices are currently not supported.</div>';
        echo '</div>';
        echo '<div class="hidden-xs hidden-sm display-md display-lg">';
        echo '<div class="alert alert-danger"><b>No Access</b><br>Sorry, you are not permitted to use this page.</div>';
        echo '</div>';
        echo '</div>';
        echo '</body>';
        echo '</html>';
        die("");
      }
		}
		
		public static function endExternalPage($spacer = '') {
      echo '<div class="display-xs hidden-sm hidden-md hidden-lg not-supported">';
      echo $spacer;
      echo '<div class="alert alert-danger"><b>Mobile devices are currently not supported.</b><br>Please use a larger screen environment.</div>';
      echo '</div>';
      echo '<div class="hidden-xs display-sm hidden-md hidden-lg not-supported">';
      echo $spacer;
      echo '<div class="alert alert-danger"><b>Tablet devices are currently not supported.</b><br>Please use a larger screen environment.</div>';
      echo '</div>';
      echo '</div>';
      echo '<div class="footer navbar-fixed-bottom hidden-xs hidden-sm display-md display-lg">';
      echo '<div class="container">';
      echo '<p class="text-muted"> <i id="loading" class="fa fa-spinner fa-spin hidden"></i> (c) '.date('Y',time()).' '.NAME.' - generated in '.number_format(microtime(true) - self::$time, 5).'s</p>';
      echo '</div>';
      echo '</div>';
			echo '</body>';
			echo '</html>';
		}
		
		public static function infobox($message) {
      return '<div class="alert alert-danger" tabindex="-1"><button type="button" class="close" data-dismiss="alert">&times;</button><span>'.$message.'</span></div>';
		}
		
		public static function startModal($title = 'Modal Title', $target = '', $method = 'GET') {
      echo '<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
      echo '<h4 class="modal-title" id="modallabel">'.$title.'</h4>';
      echo '</div>';
      echo '<div class="modal-body" id="modal-body">';
      echo '<form class="form-horizontal" id="data" data-async data-target="#modal-content" action="'.$target.'" method="'.strtoupper($method).'"><fieldset>';
    }
    
    public static function endModal($title = null, $color = 'primary') {       
      echo '</fieldset></form>';
      echo '</div>';
      echo '<div class="modal-footer">';
      echo '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
      if($title != null) {
        echo '<button form="data" class="btn btn-'.$color.'" type="submit">'.$title.'</button>';
      }
      echo '</div>';
    }
	}

?>