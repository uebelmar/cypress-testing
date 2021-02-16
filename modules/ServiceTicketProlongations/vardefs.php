<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ServiceTicketProlongation'] = array(
    'table' => 'serviceticketprolongations',
    'audited' =>  false,
    'duplicate_merge' =>  false,
    'unified_search' =>  false,
	'fields' => array(
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
            'relationship' => 'serviceticket_serviceticketprolongations',
            'link_type' => 'one',
            'source' => 'non-db',
            'vname' => 'LBL_SERVICETICKETS',
        ),
        'prolonged_until' => array(
            'name' => 'prolonged_until',
            'vname' => 'LBL_PROLONGED_UNTIL',
            'type' => 'date'
        )
    ),
	'relationships' => array(
        'serviceticket_serviceticketprolongations' => array(
            'lhs_module' => 'ServiceTickets',
            'lhs_table' => 'servicetickets',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceTicketProlongations',
            'rhs_table' => 'serviceticketprolongations',
            'rhs_key' => 'serviceticket_id',
            'relationship_type' => 'one-to-many'
        )
	),
    'indices' => array(
        array('name' => 'idx_serviceticketprolongations_ticketid', 'type' => 'index', 'fields' => array('serviceticket_id')),
    )
);

VardefManager::createVardef('ServiceTicketProlongations', 'ServiceTicketProlongation', array('default', 'assignable'));
