<?php

	require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

  core::displayHeader();
  core::startPage();
  core::startModule( 0, 0);
  
  table::toolbar(array('<button class="btn btn-success" data-toggle="modal" href="d.php?a=add" data-target="#modal"> <i class="fa fa-plus"></i> Add Bot</button>'));
  
  $fields = array(
              array('id' => 'ID',
                    'name' => 'ID',
                    'width' => '50px'),
              array('id' => 'AUTOSTART',
                    'name' => 'AS',
                    'width' => '40px',
                    'align' => 'center',
                    'format' => 'rowautostart'),      
              array('id' => 'STATE',
                    'name' => '',
                    'width' => '40px',
                    'format' => 'rowstate'),      
              array('id' => 'NAME',
                    'name' => 'Bot Name',
                    'sort' => true),
              array('id' => 'COLOR',
                    'name' => 'Color'),
              array('id' => 'OWNER',
                    'name' => 'Owner',
                    'width' => '50px'),
              array('id' => 'INSERTED',
                    'name' => 'Inserted',
                    'width' => '160px'),
              array('id' => 'UPDATED',
                    'name' => 'Updated',
                    'width' => '160px'),
              array('id' => 'ID',
                    'name' => '',
                    'format' => 'rowcontrol',
                    'width' => '120px',
                    'align' => 'right'),         
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