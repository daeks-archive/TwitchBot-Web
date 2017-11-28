<?php

	require_once(dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'config.php');

  core::displayHeader();
  core::startPage();
  core::startModule();

  table::toolbar(array('<button class="btn btn-success" data-toggle="modal" href="d.php?a=add" data-target="#modal"> <i class="fa fa-plus"></i> Add Command</button>'));
  
  $fields = array(
              array('id' => 'ID',
                    'name' => 'ID',
                    'width' => '50px'),    
              array('id' => 'NAME',
                    'name' => 'Command',
                    'sort' => true),
              array('id' => 'INSERTED',
                    'name' => 'Inserted',
                    'width' => '160px'),
              array('id' => 'ID',
                    'name' => '',
                    'format' => 'rowaction',
                    'width' => '50px',
                    'align' => 'right')
            );
  
  table::render(CONTROL.'?a=init', $fields);

  core::endModule(true);
  core::endPage();
	
?>