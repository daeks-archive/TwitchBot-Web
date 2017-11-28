<?php

	require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

  core::displayHeader();
  core::startPage();
  core::startModule(auth::getBotId(), 0);

  table::toolbar(array('<button class="btn btn-success" data-toggle="modal" href="d.php?a=add" data-target="#modal"> <i class="fa fa-plus"></i> Add Channel</button>'));
  
  $fields = array(
              array('id' => 'ID',
                    'name' => 'ID',
                    'width' => '50px'), 
              array('id' => 'MUTE',
                    'name' => '',
                    'width' => '40px',
                    'format' => 'rowstate'),      
              array('id' => 'NAME',
                    'name' => 'Channel Name',
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