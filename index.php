<?php

  require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');
  utils::construct();
  
  header('Location: '.URL_SEPARATOR.START);
 
?>