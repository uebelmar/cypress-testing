<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ServiceQueue'] = array(
    'table' => 'servicequeues',
    'comment' => 'ServiceQueues Module',
    'audited' =>  false,
    'duplicate_merge' =>  false,
    'unified_search' =>  false,
	
	'fields' => array(
        'servicetickets' => array(
            'vname' => 'LBL_SERVICETICKETS',
            'name' => 'servicetickets',
            'type' => 'link',
            'module' => 'ServiceTickets',
            'relationship' => 'servicetickets_servicequeues',
            'source' => 'non-db'
        ),
        'servicecalls' => array(
            'vname' => 'LBL_SERVICECALLS',
            'name' => 'servicecalls',
            'type' => 'link',
            'module' => 'ServiceCalls',
            'relationship' => 'servicecalls_servicequeues',
            'source' => 'non-db'
        ),
        'users' => array(
            'vname' => 'LBL_USERS',
            'name' => 'users',
            'type' => 'link',
            'module' => 'Users',
            'bean_name' => 'User',
            'relationship' => 'servicequeues_users',
            'source' => 'non-db'
        )

    ),
	'relationships' => array(

	),
	'indices' => array(
	)
);

VardefManager::createVardef('ServiceQueues', 'ServiceQueue', array('default', 'assignable'));
