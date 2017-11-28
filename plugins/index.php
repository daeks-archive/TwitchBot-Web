<?php

	require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

  core::displayHeader();
  core::startPage();
  core::startModule();

  $fields = array(
              array('id' => 'ID',
                    'name' => '',
                    'width' => '40px',
                    'format' => 'rowstate'),      
              array('id' => 'NAME',
                    'name' => 'Plugin Name',
                    'sort' => true,
                    'format' => 'rowname'),
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
  
  table::render(CONTROL.'?a=init', $fields, 0, false);


  core::endModule();
  core::endPage();
	
?>