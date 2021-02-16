<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['SystemHolidayCalendar'] = array(
    'table' => 'systemholidaycalendar',
    'comment' => 'SystemHolidayCalendar Module',
    'audited' => false,
    'fields' => array(
        'servicecalendars' => array(
            'vname' => 'LBL_SERVICECALENDARS',
            'name' => 'servicecalendars',
            'type' => 'link',
            'module' => 'ServiceCalendars',
            'relationship' => 'systemholidaycalendar_servicecalendars',
            'link_type' => 'one',
            'source' => 'non-db'
        ),
        'systemholidaycalendardays' => array(
            'vname' => 'LBL_SYSTEMHOLIDAYCALENDARDAYS',
            'name' => 'systemholidaycalendardays',
            'type' => 'link',
            'module' => 'SystemHolidayCalendarDays',
            'relationship' => 'systemholidaycalendar_systemholidaycalendardays',
            'link_type' => 'one',
            'source' => 'non-db'
        ),
    ),
    'relationships' => array(
        'systemholidaycalendar_servicecalendars' => array(
            'lhs_module' => 'SystemHolidayCalendars',
            'lhs_table' => 'systemholidaycalendars',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceCalendars',
            'rhs_table' => 'servicecalendars',
            'rhs_key' => 'systemholidaycalendar_id',
            'relationship_type' => 'one-to-many'
        )
    )
);

VardefManager::createVardef('SystemHolidayCalendars', 'SystemHolidayCalendar', array('default'));
