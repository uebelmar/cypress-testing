<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ServiceTicketNote'] = array(
    'table' => 'serviceticketnotes',
    'comment' => 'ServiceTicketNotes Module',
    'audited' =>  true,
	'fields' => array(
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NOTE',
            'type' => 'varchar',
            'len' => 100,
            'required' => false
        ),
        'servicenote_status' => array(
            'name' => 'servicenote_status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'options' => 'servicenote_status_dom',
            'len' => 6,
            'default' => 'read'
        ),
        'description' => array(
            'name' => 'description',
            'vname' => 'LBL_DESCRIPTION',
            'type' => 'richtext',
            'dbtype' => 'text',
            'required' => true
        ),
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
            'relationship' => 'serviceticket_serviceticketnotes',
            'link_type' => 'one',
            'source' => 'non-db',
            'vname' => 'LBL_SERVICETICKETS',
        ),
    ),
	'relationships' => array(
        'serviceticket_serviceticketnotes' => array(
            'lhs_module' => 'ServiceTickets',
            'lhs_table' => 'servicetickets',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceTicketNotes',
            'rhs_table' => 'serviceticketnotes',
            'rhs_key' => 'serviceticket_id',
            'relationship_type' => 'one-to-many'
        )
	),
    'indices' => array(
        array('name' => 'idx_serviceticketnotes_ticketid', 'type' => 'index', 'fields' => array('serviceticket_id')),
    )
);

VardefManager::createVardef('ServiceTicketNotes', 'ServiceTicketNote', array('default', 'assignable'));
