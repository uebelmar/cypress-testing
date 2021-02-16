<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['SystemHolidayCalendarDay'] = array(
    'table' => 'systemholidaycalendardays',
    'comment' => 'SystemHolidayCalendarDay Module',
    'audited' => false,
    'fields' => array(
        'holiday_date' => array(
            'name' => 'holiday_date',
            'vname' => 'LBL_DATE',
            'type' => 'date',
            'required' => true,
            'comment' => 'the date of the holiday, for recurring holiday pick any occurence'
        ),
        'annually' => array(
            'name' => 'annually',
            'vname' => 'LBL_ANNUALLY',
            'type' => 'bool',
            'comment' => 'if the same holiday occurs every year on that date'
        ),
        'closed_from' => array(
            'name' => 'closed_from',
            'vname' => 'LBL_CLOSED_FROM',
            'type' => 'varchar',
            'len' => 4,
            'comment' => 'set the time when your business is closed from - for Holidays where you might close e.g. only half a day'
        ),
        'closed_until' => array(
            'name' => 'closed_until',
            'vname' => 'LBL_CLOSED_UNTIL',
            'type' => 'varchar',
            'len' => 4,
            'comment' => 'set the time when your business is closed until - for Holidays where you might close e.g. only half a day'
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
            'relationship' => 'systemholidaycalendar_systemholidaycalendardays',
            'link_type' => 'one',
            'source' => 'non-db'
        ),
    ),
    'relationships' => array(
        'systemholidaycalendar_systemholidaycalendardays' => array(
            'lhs_module' => 'SystemHolidayCalendars',
            'lhs_table' => 'systemholidaycalendars',
            'lhs_key' => 'id',
            'rhs_module' => 'SystemHolidayCalendarDays',
            'rhs_table' => 'systemholidaycalendardays',
            'rhs_key' => 'systemholidaycalendar_id',
            'relationship_type' => 'one-to-many'
        )
    )
);

VardefManager::createVardef('SystemHolidayCalendarDays', 'SystemHolidayCalendarDay', array('default'));
