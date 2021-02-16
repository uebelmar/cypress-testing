<?php

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['TravelSegment'] =[
   'table' => 'travelsegments',
   'fields' => [
        'date_start' => [
            'name' => 'date_start',
            'type' => 'datetime',
            'vname' => 'LBL_DATE_START',
        ],
      
        'date_end' => [
            'name' => 'date_end',
            'type' => 'datetime',
            'vname' => 'LBL_DATE_END',
        ],

        'travel_id' => [
            'name' => 'travel_id',
            'type' => 'id',
            'vname' => 'LBL_TRAVEL',
        ],

        'number_passengers' => [
            'name' => 'number_passengers',
            'type' => 'int',
            'len' => 2,
            'vname' => 'LBL_PASSENGERS'
        ],

        'travel_name' =>[
            'name' => 'travel_name',
            'type' => 'relate',
            'link' => 'travel',
            'rname' => 'name',
            'id_name' => 'travel_id',
            'module' => 'Travels',
            'source' => 'non-db'
        ],

        'travel' => [
            'name' => 'travel',
            'type' => 'link',
            'module' => 'Travels',
            'source'  => 'non-db',
            'relationship' => 'traveltravelsegments'
        ],

        'distance' => [
            'name' => 'distance',
            'type' => 'int',
            'len' => 6,
            'vname' => 'LBL_DISTANCE',
        ],
        
        'border_crossing_time' => [
            'name' => 'border_crossing_time',
            'type' => 'datetime',
            'vname' => 'LBL_BORDER_CROSSING_TIME',
        ], 
        'transport_type' => [
            'name' => 'transport_type',
            'type' => 'enum',       
            'len' => 20,
            'vname' => 'LBL_TRANSPORT_TYPE',
            'options' => 'transport_type_dom',
        ],
        'start_country' => [
            'name' => 'start_country',
            'type' => 'char',
            'len' => 2,
            'vname' => 'LBL_COUNTRY_START',
        ],
        'end_country' => [
            'name' => 'end_country',
            'type' => 'char',
            'len' => 2,
            'vname' => 'LBL_COUNTRY_END',
        ],
        'daily_rate' => [
            'name' => 'daily_rate',
            'type' => 'currency',
            'vname' => 'LBL_DAILY_RATE',
        ],
        'currency_id' => [
            'name' => 'currency_id',
            'type' => 'id',
            'vname' => 'LBL_CURRENCY',
        ]
    ]
];

VardefManager::createVardef('TravelSegments', 'TravelSegment', array('default', 'assignable'));
