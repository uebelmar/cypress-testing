<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ServiceTicketSLATime'] = array(
    'table' => 'serviceticketslatimes',
    'comment' => 'ServiceTicketSLATimes Module',
    'audited' => true,
    'unified_search' => false,
    'fields' => array(
        'serviceticketsla_id' => array(
            'name' => 'serviceticketsla_id',
            'vname' => 'LBL_SERVICETICKETSLA_ID',
            'type' => 'id',
            'required' => true,
            'comment' => ' the id of the serviceticketSLA'
        ),
        'serviceticketsla_name' => array(
            'name' => 'serviceticketsla_name',
            'vname' => 'LBL_SERVICETICKETSLA',
            'type' => 'relate',
            'source' => 'non-db',
            'len' => '255',
            'id_name' => 'serviceticketsla_id',
            'module' => 'ServiceTicketSLAs',
            'link' => 'serviceticketsla',
            'join_name' => 'serviceticketsla',
        ),
        'serviceticketsla' => array(
            'vname' => 'LBL_SERVICETICKETSLA',
            'name' => 'serviceticketsla',
            'type' => 'link',
            'module' => 'ServiceTicketSLAs',
            'relationship' => 'serviceticketsla_serviceticketslatimes',
            'link_type' => 'one',
            'source' => 'non-db'
        ),
        'active' => array(
            'name' => 'active',
            'vname' => 'LBL_ACTIVE',
            'type' => 'bool',
            'default' => true,
            'comment' => ' indicates if the SLA line is active'
        ),
        'serviceticket_type' => array(
            'name' => 'serviceticket_type',
            'vname' => 'LBL_TYPE',
            'type' => 'enum',
            'len' => 25,
            'options' => 'servicecall_type_dom',
            'comment' => 'the ticket type for reference selection'
        ),
        'serviceticket_class' => array(
            'name' => 'serviceticket_class',
            'vname' => 'LBL_CLASS',
            'type' => 'enum',
            'len' => 100,
            'options' => 'serviceticket_class_dom',
            'comment' => 'Class (ex: severe, moderate, low impact)',
        ),
        'time_to_response' => array(
            'name' => 'time_to_response',
            'vname' => 'LBL_TIME_TO_RESPONSE',
            'type' => 'int',
            'comment' => 'the time to response'
        ),
        'time_to_response_unit' => array(
            'name' => 'time_to_response_unit',
            'vname' => 'LBL_TIME_TO_RESPONSE_UNIT',
            'type' => 'varchar',
            'len' => 3,
            'comment' => 'the time unit for the response time'
        ),
        'time_to_resolution' => array(
            'name' => 'time_to_resolution',
            'vname' => 'LBL_TIME_TO_RESOLUTION',
            'type' => 'int',
            'comment' => 'the time to rewsolution'
        ),
        'time_to_resolution_unit' => array(
            'name' => 'time_to_resolution_unit',
            'vname' => 'LBL_TIME_TO_RESOLUTION_UNIT',
            'type' => 'varchar',
            'len' => 3,
            'comment' => 'the time unit for the resolution time'
        )
    ),
    'relationships' => array(
        'serviceticketsla_serviceticketslatimes' => array(
            'lhs_module' => 'ServiceTicketSLAs',
            'lhs_table' => 'serviceticketslas',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceTicketSLATimes',
            'rhs_table' => 'serviceticketslatimes',
            'rhs_key' => 'serviceticketsla_id',
            'relationship_type' => 'one-to-many'
        )
    )
);

VardefManager::createVardef('ServiceTicketSLATimes', 'ServiceTicketSLATime', array('default'));
