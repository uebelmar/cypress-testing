<?php
$dictionary['starfacecalls'] = array(
    'table' => 'starfacecalls',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'id'
        ),
        'channel' => array(
            'name' => 'channel',
            'type' => 'varchar',
            'len' => 20
        ),
        'callednumber' => array(
            'name' => 'callednumber',
            'type' => 'varchar',
            'len' => 50
        ),
        'callernumber' => array(
            'name' => 'callernumber',
            'type' => 'varchar',
            'len' => 50
        ),
        'callstate' => array(
            'name' => 'callstate',
            'type' => 'varchar',
            'len' => 50
        ),
        'calldirection' => array(
            'name' => 'calldirection',
            'type' => 'varchar',
            'len' => 10
        )
    ),
    'indices' => array(
        array(
            'name' => 'sstarfacecallspk',
            'type' => 'primary',
            'fields' => array('id')
        )
    )
);
