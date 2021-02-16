<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ServiceCalendar'] = array(
    'table' => 'servicecalendars',
    'comment' => 'ServiceCalendars Module',
    'audited' => false,
    'unified_search' => false,
    'fields' => array(
        'timezone' => array(
            'name' => 'timezone',
            'vname' => 'LBL_TIMEZONE',
            'type' => 'varchar',
            'len' => 50,
            'required' => true,
            'comment' => 'the timezone for time entries for this calendar'
        ),
        'systemholidaycalendar_id' => array(
            'name' => 'systemholidaycalendar_id',
            'vname' => 'LBL_SYSTEMHOLIDAYCALENDAR_ID',
            'type' => 'id',
            'required' => false,
            'comment' => 'the id of the ServiceCalendar'
        ),
        'systemholidaycalendar_name' => array(
            'name' => 'systemholidaycalendar_name',
            'vname' => 'LBL_SYSTEMHOLIDAYCALENDAR',
            'type' => 'relate',
            'source' => 'non-db',
            'len' => '255',
            'id_name' => 'systemholidaycalendar_id',
            'module' => 'SystemHolidayCalendars',
            'link' => 'systemholidaycalendar',
            'join_name' => 'systemholidaycalendar',
        ),
        'systemholidaycalendar' => array(
            'vname' => 'LBL_SYSTEMHOLIDAYCALENDAR',
            'name' => 'systemholidaycalendar',
            'type' => 'link',
            'module' => 'SystemHolidayCalendars',
            'relationship' => 'systemholidaycalendar_servicecalendars',
            'link_type' => 'one',
            'source' => 'non-db'
        ),
        'serviceticketsla' => array(
            'vname' => 'LBL_SERVICETICKETSLA',
            'name' => 'serviceticketsla',
            'type' => 'link',
            'module' => 'ServiceTicketSLAs',
            'relationship' => 'servicecalendar_serviceticketslas',
            'link_type' => 'one',
            'source' => 'non-db'
        )
    ),
    'relationships' => array(
        'servicecalendar_serviceticketslas' => array(
            'lhs_module' => 'ServiceCalendars',
            'lhs_table' => 'servicecalendars',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceTicketSLAs',
            'rhs_table' => 'serviceticketslas',
            'rhs_key' => 'servicecalendar_id',
            'relationship_type' => 'one-to-many'
        )
    )
);

VardefManager::createVardef('ServiceCalendars', 'ServiceCalendar', array('default'));
