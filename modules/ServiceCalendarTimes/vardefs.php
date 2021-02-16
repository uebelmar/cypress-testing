<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ServiceCalendarTime'] = array(
    'table' => 'servicecalendartimes',
    'comment' => 'ServiceCalendarTimes Module',
    'audited' => false,
    'unified_search' => false,
    'fields' => array(
        'dayofweek' => array(
            'name' => 'dayofweek',
            'type' => 'varchar',
            'len' => '1',
            'comment' => 'ISO-8601 numeric representation of the day of the week, 1 (for Monday) through 7 (for Sunday)'
        ),
        'timestart' => array(
            'name' => 'timestart',
            'type' => 'varchar',
            'len' => '4',
            'comment' => 'working start time in military format (e.g. 8000 for 8:00am or 2300 for 11pm)'
        ),
        'timeend' => array(
            'name' => 'timeend',
            'type' => 'varchar',
            'len' => '4',
            'comment' => 'working end time in military format (e.g. 8000 for 8:00am or 2300 for 11pm)'
        ),
        'servicecalendar_id' => array(
            'name' => 'servicecalendar_id',
            'vname' => 'LBL_SERVICECALENDAR_ID',
            'type' => 'id',
            'required' => true,
            'comment' => 'the id of the ServiceCalendar'
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
            'relationship' => 'servicecalendar_servicecalendartimes',
            'link_type' => 'one',
            'source' => 'non-db'
        )
    ),
    'relationships' => array(
        'servicecalendar_servicecalendartimes' => array(
            'lhs_module' => 'ServiceCalendars',
            'lhs_table' => 'servicecalendars',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceCalendarTimes',
            'rhs_table' => 'servicecalendartimes',
            'rhs_key' => 'servicecalendar_id',
            'relationship_type' => 'one-to-many'
        )
    )
);

VardefManager::createVardef('ServiceCalendarTimes', 'ServiceCalendarTime', array('default'));
