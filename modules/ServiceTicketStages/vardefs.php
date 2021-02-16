<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ServiceTicketStage'] = array(
    'table' => 'serviceticketstages',
    'audited' =>  false,
    'duplicate_merge' =>  false,
    'unified_search' =>  false,
	
	'fields' => array(
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_TOPIC',
            'type' => 'varchar',
            'len' => 100,
            'requied' => false

        ),
	    //account
	    'serviceticket_id' => array(
            'name' => 'serviceticket_id',
            'vname' => 'LBL_SERVICETICKET_ID',
            'type' => 'id',
        ),
        'serviceticket_name' => array(
            'name' => 'serviceticket_name',
            'vname' => 'LBL_SERVICETICKET',
            'type' => 'relate',
            'source' => 'non-db',
            'len' => '255',
            'id_name' => 'serviceticket_id',
            'rname' => 'name',
            'module' => 'ServiceTickets',
            'link' => 'servicetickets',
            'join_name' => 'servicetickets'
        ),
        'servicetickets' => array(
            'name' => 'servicetickets',
            'module' => 'ServiceTickets',
            'type' => 'link',
            'relationship' => 'serviceticket_serviceticketstages',
            'link_type' => 'one',
            'source' => 'non-db',
            'vname' => 'LBL_SERVICETICKETS',
        ),
        'serviceticket_status' => array (
            'name' => 'serviceticket_status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'len' => 100,
            'options' => 'serviceticket_status_dom',
            'comment' => 'Status (ex: new, planned, scheduled, completed, cancelled)',
        ),
        'serviceticket_class' => array (
            'name' => 'serviceticket_class',
            'vname' => 'LBL_CLASS',
            'type' => 'enum',
            'len' => 100,
            'options' => 'serviceticket_class_dom',
            'comment' => 'Class (ex: severe, moderate, low impact)',
        ),
        //servicequeue
        'servicequeue_id' => array(
            'name' => 'servicequeue_id',
            'vname' => 'LBL_SERVICEQUEUE_ID',
            'type' => 'id',
        ),
        'servicequeue_name' => array(
            'name' => 'servicequeue_name',
            'vname' => 'LBL_SERVICEQUEUE',
            'type' => 'relate',
            'source' => 'non-db',
            'len' => '255',
            'id_name' => 'servicequeue_id',
            'rname' => 'name',
            'module' => 'ServiceQueues',
            'link' => 'servicequeues',
            'join_name' => 'servicequeues',
            'required'=> false,
        ),
        'servicequeues' => array(
            'vname' => 'LBL_SERVICEQUEUES',
            'name' => 'servicequeues',
            'type' => 'link',
            'module' => 'ServiceQueues',
            'relationship' => 'serviceticketstages_servicequeues',
            'source' => 'non-db'
        ),
        //categories
        'sysservicecategory_id1' => array(
            'name' => 'sysservicecategory_id1',
            'vname' => 'LBL_SYSSERVICECATEGORY_ID1',
            'type' => 'id',
        ),
        'sysservicecategory_id2' => array(
            'name' => 'sysservicecategory_id2',
            'vname' => 'LBL_SYSSERVICECATEGORY_ID2',
            'type' => 'id',
        ),
        'sysservicecategory_id3' => array(
            'name' => 'sysservicecategory_id3',
            'vname' => 'LBL_SYSSERVICECATEGORY_ID3',
            'type' => 'id',
        ),
        'sysservicecategory_id4' => array(
            'name' => 'sysservicecategory_id4',
            'vname' => 'LBL_SYSSERVICECATEGORY_ID4',
            'type' => 'id',
        )
    ),
	'relationships' => array(
        'serviceticket_serviceticketstages' => array(
            'lhs_module' => 'ServiceTickets',
            'lhs_table' => 'servicetickets',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceTicketStages',
            'rhs_table' => 'serviceticketstages',
            'rhs_key' => 'serviceticket_id',
            'relationship_type' => 'one-to-many'
        ),
        'serviceticketstages_servicequeues' => array(
            'lhs_module' => 'ServiceQueues',
            'lhs_table' => 'servicequeues',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceTicketStages',
            'rhs_table' => 'serviceticketstages',
            'rhs_key' => 'servicequeue_id',
            'relationship_type' => 'one-to-many'
        )
	),
    'indices' => array(
        array('name' => 'idx_serviceticketstages_ticketid', 'type' => 'index', 'fields' => array('serviceticket_id')),
    )
);

VardefManager::createVardef('ServiceTicketStages', 'ServiceTicketStage', array('default', 'assignable'));
