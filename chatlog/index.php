<?php

	require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

  core::displayHeader();
  core::startPage();
  core::startModule();
  
  $fields = array(
              array('id' => 'INSERTED',
                    'name' => 'Inserted',
                    'width' => '160px'),
              array('id' => 'INSERTBY',
                    'width' => '100px',
                    'name' => 'User',
                    'format' => 'rowuser'),           
              array('id' => 'VALUE',
                    'name' => 'Message',
                    'format' => 'rowlog'),   
              array('id' => 'TYPE',
                    'name' => 'Type',
                    'width' => '40px')
            );
  
  table::render(CONTROL.'?a=init', $fields, 25);


  core::endModule();
  core::endPage();
	
?>