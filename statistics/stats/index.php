<?php

	require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

  core::displayHeader();
  core::startPage();
  core::startModule();
  
  $fields = array(
              array('id' => 'ID',
                    'name' => 'ID',
                    'width' => '50px'),    
              array('id' => 'NAME',
                    'name' => 'Command',
                    'sort' => true),
              array('id' => 'VALUE',
                    'name' => 'Amount',
                    'sort' => true),
              array('id' => 'INSERTED',
                    'name' => 'Inserted',
                    'width' => '160px'),
              array('id' => 'UPDATED',
                    'name' => 'Updated',
                    'width' => '160px'),
              array('id' => 'ID',
                    'name' => '',
                    'format' => 'rowaction',
                    'width' => '180px',
                    'align' => 'right')
            );
  
  table::render(CONTROL.'?a=init', $fields);


  core::endModule();
  core::endPage();
	
?>