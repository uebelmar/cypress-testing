<?php

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['Travel'] =[
    'table' => 'travels',
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
        'costcenter_id' => [
            'name' => 'costcenter_id',
            'type' => 'id',
            'vname' => 'LBL_COSTCENTER',
        ],
        'location' => [
            'name' => 'location',
            'type' => 'varchar',
            'len' => 150,
            'vname' => 'LBL_LOCATION',
        ],
        'amount' => [
            'name' => 'amount',
            'type' => 'currency',
            'vname' => 'LBL_AMOUNT'
        ],

        'totaldistance' => [
            'name' => 'totaldistance',
            'type' => 'int',
            'len' => 6,
            'vname' => 'LBL_TOTAL_DISTANCE'
        ],
      
        'currency_id' => [
            'name' => 'currency_id',
            'type' => 'id',
            'vname' => 'LBL_CURRENCY'
        ],
        'travelreceipts' => [
            'name' => 'travelreceipts',
            'type' => 'link',
            'source' => 'non-db',
            'relationship' => 'travelreceipts_travel',
            'module' => 'TravelReceipts',
        ],
        'travelsegments' => [
            'name' => 'travelsegments',
            'type' => 'link',
            'module' => 'TravelSegments',
            'source' => 'non-db',
            'relationship' => 'traveltravelsegments',
        ]
    ],
    'relationships' => [
        'traveltravelsegments' => [
            'lhs_module' => 'Travels',
            'lhs_table' => 'travels',
            'lhs_key' => 'id',
            'rhs_module' => 'TravelSegments',
            'rhs_table' => 'travelsegments',
            'rhs_key' => 'travel_id',
            'relationship_type' => 'one-to-many',               

        ]
    ]
];



VardefManager::createVardef('Travels', 'Travel', array('default', 'assignable'));
