<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ServiceTicketSLA'] = array(
    'table' => 'serviceticketslas',
    'comment' => 'ServiceTicketSLAs Module',
    'audited' =>  true,
    'unified_search' =>  false,
	'fields' => array(
        'active' => array(
            'name' => 'active',
            'vname' => 'LBL_ACTIVE',
            'type' => 'bool',
            'default' => true,
            'comment' => ' indicates if the SLA line is active'
        ),
        'is_default' => array(
          'name' => 'is_default',
          'type' => 'bool',
          'vname' => 'LBL_IS_DEFAULT'
        ),
        /**
         * for the link to the calendar
         */
        'servicecalendar_id' => array(
            'name' => 'servicecalendar_id',
            'vname' => 'LBL_SERVICECALENDAR_ID',
            'type' => 'id',
            'required' => true,
            'comment' => ' the id of the ServiceCalendar'
        ),
        'servicecalendar_name' => array(
            'name' => 'servicecalendar_name',
            'vname' => 'LBL_SERVICECALENDAR',
            'type' => 'relate',
            'source' => 'non-db',
            'len' => '255',
            'id_name' => 'servicecalendar_id',
            'module' => 'ServiceCalendars',
            'link' => 'servicecalendar',
            'join_name' => 'servicecalendar',
        ),
        'servicecalendar' => array(
            'vname' => 'LBL_SERVICECALENDAR',
            'name' => 'servicecalendar',
            'type' => 'link',
            'module' => 'ServiceCalendars',
            'relationship' => 'servicecalendar_serviceticketslas',
            'link_type' => 'one',
            'source' => 'non-db'
        ),
        'serviceticketslatimes' => array(
            'vname' => 'LBL_SERVICETICKETSLATIMES',
            'name' => 'serviceticketslatimes',
            'type' => 'link',
            'module' => 'ServiceTicketSLATimes',
            'relationship' => 'serviceticketsla_serviceticketslatimes',
            'link_type' => 'one',
            'source' => 'non-db'
        ),
        'servicetickets' => array(
            'vname' => 'LBL_SERVICETICKETS',
            'name' => 'servicetickets',
            'type' => 'link',
            'module' => 'ServiceTickets',
            'relationship' => 'serviceticketslas_servicetickets',
            'link_type' => 'one',
            'source' => 'non-db'
        )
    ),
    'relationships' => array(
        'serviceticketslas_servicetickets' => array(
            'lhs_module' => 'ServiceTicketSLAs',
            'lhs_table' => 'serviceticketslas',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceTickets',
            'rhs_table' => 'servicetickets',
            'rhs_key' => 'serviceticketsla_id',
            'relationship_type' => 'one-to-many'
        )
    )
);

VardefManager::createVardef('ServiceTicketSLAs', 'ServiceTicketSLA', array('default'));
