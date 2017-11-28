<?php

	require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

  core::displayHeader();
  core::startPage();
  core::startModule(0, 0);

  $fields = array(
              array('id' => 'NAME',
                    'name' => 'Command',
                    'sort' => true),
              array('id' => 'VALUE',
                    'name' => 'Description'),
              array('id' => 'SYNTAX',
                    'name' => 'Syntax'),
              array('id' => 'LEVEL',
                    'name' => 'Level')
            );
  
  table::render(CONTROL.'?a=init', $fields, 0, false);


  core::endModule();
  core::endPage();
	
?>