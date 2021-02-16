<?php

/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

$dictionary['emailschedules_beans'] = array (
// TODO: EMAIL ID
    'table' => 'emailschedules_beans',
    'fields' => array(
        array (
            'name' => 'id',
            'type' => 'varchar',
            'len' => '36',
        ),
        array(
            'name' => 'emailschedule_status',
            'type' => 'enum',
            'options' => 'emailschedule_status_dom',
            'len' => 50,
            'comment' 	=> 'Status of the email schedule',
        ),
        array(
            'name'		=> 'emailschedule_id',
            'type'		=> 'varchar',
            'dbType'	=> 'id',
            'len'		=> '36',
            'comment' 	=> 'FK to emailschedules table',
        ),
        array(
            'name'		=> 'bean_module',
            'type'		=> 'varchar',
            'len'		=> '100',
            'comment' 	=> 'bean\'s module',
        ),
        array(
            'name'		=> 'bean_id',
            'dbType'	=> 'id',
            'type'		=> 'varchar',
            'len'		=> '36',
            'comment' 	=> 'FK to various beans\'s tables',
        ),
        array(
            'name'		=> 'email_id',
            'type'		=> 'varchar',
            'dbType'	=> 'id',
            'len'		=> '36',
            'comment' 	=> 'FK to email table',
        ),
        array (
            'name' => 'date_modified',
            'type' => 'datetime'
        ),
        array (
            'name' => 'deleted',
            'type' => 'bool',
            'len' => '1',
            'default' => '0'
        ),

    ),
    'relationships' => array(
    ),
    'indices' => array(
        array(
            'name'		=> 'emailschedules_beanspk',
            'type'		=> 'primary',
            'fields'	=> array('id')
        ),
        array(
            'name'		=> 'idx_emailschedules_beans_bean_id',
            'type'		=> 'index',
            'fields'	=> array('bean_id')
        ),
        array(
            'name'		=> 'idx_emailschedules_beans_emailschedule_bean',
            'type'		=> 'alternate_key',
            'fields'	=> array('emailschedule_id', 'bean_id', 'deleted')
        ),
    )
);
