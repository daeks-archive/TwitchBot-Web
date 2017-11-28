<?php

	require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

  core::displayHeader();
  core::startPage();
  core::startModule(0, 0);
  
  $fields = array(
              array('id' => 'BOT',
                    'name' => 'Bot',
                    'width' => '100px'),
              array('id' => 'CHANNEL',
                    'name' => 'Channel',
                    'width' => '100px'),
              array('id' => 'INSERTED',
                    'name' => 'Inserted',
                    'width' => '160px'),
              array('id' => 'INSERTBY',
                    'width' => '100px',
                    'name' => 'User'),  
              array('id' => 'VALUE',
                    'name' => 'Message'),
              array('id' => 'NAME',
                    'name' => 'Mode',
                    'width' => '40px'),
              array('id' => 'TYPE',
                    'name' => 'Type',
                    'width' => '40px')
            );
  
  table::render(CONTROL.'?a=init', $fields, 25);


  core::endModule();
  core::endPage();
	
?>