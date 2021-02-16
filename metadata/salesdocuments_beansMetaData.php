<?php

$dictionary['salesdocuments_beans'] = array(
    'table' => 'salesdocuments_beans',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'varchar',
            'dbType' => 'id',
            'len' => '36'
        ),
        'salesdocument_id' => array(
            'name' => 'salesdocument_id',
            'type' => 'varchar',
            'dbType' => 'id',
            'len' => '36'
        ),
        'bean_id' => array(
            'name' => 'bean_id',
            'dbType' => 'id',
            'type' => 'varchar',
            'len' => '36'
        ),
        'bean_module' => array(
            'name' => 'bean_module',
            'type' => 'varchar',
            'len' => '100'
        ),
        'date_modified' => array(
            'name' => 'date_modified',
            'type' => 'datetime'
        ),
        'deleted' => array(
            'name' => 'deleted',
            'type' => 'bool',
            'len' => '1',
            'default' => '0',
            'required' => false
        )
    ),
    'indices' => array(
        array(
            'name' => 'salesdocuments_beanspk',
            'type' => 'primary',
            'fields' => array('id')
        )
    )
);
