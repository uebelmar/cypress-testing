<?php

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['TravelReceipt'] = [
    'table' => 'travelreceipts',
    'fields' => [
        'receipt_type' =>[
            'name' => 'receipt_type',
            'type' => 'enum',
            'len' => '20',
            'vname' => 'LBL_RECEIPTS_TYPE',
            'options' => 'receipts_dom',
            ],

        'payment_type' =>[
            'name' => 'payment_type',
            'type' => 'text',
            'len' => '20',
            'vname' => 'LBL_PAYMENTS_TYPE',
            'options' => 'payments_type_dom',
        ],
        /*'receipt' => [
            'name' => 'receipt',
            'type' => 'varchar',
            'len' => 150,
            'vname' => 'LBL_RECEIPT',
        ],*/
        'costcenter_id' => [
             'name' => 'costcenter_id',
             'type' => 'id',
             'vname' => 'LBL_COSTCENTER',
            ],
        'costcenter_number' => [            //cost center number I need in my table
            'name' => 'costcenter_number',
            'type' => 'relate',
            'id_name' => 'costcenter_id',
            'rname' => 'costcenter_number',
            'module' => 'CostCenters', //name of module Cost Center, with space?
            'link' => 'costcenter_link',
            'source' => 'non-db',       //will not save in database
        ],
        'costcenter_link' => [
            'name' => 'costcenter_link',
            'type' => 'link',
            'source' => 'non-db',
            'relationship' => 'costcenter',
            'module' => 'CostCenters',      //name of the module for the relationship
            ],
        'travel_id' => [
            'name' => 'travel_id',
            'type' => 'id',
            'vname' => 'LBL_TRAVEL',
            ],
        'travel_name' => [
            'name' => 'travel_name',
            'type' => 'relate',
            'id_name' => 'travel_id',
            'rname' => 'name',
            'module' => 'Travels',
            'link' => 'travel_link',
            'source' => 'non-db',
            ],
        'travel_link' => [
            'name' => 'travel_link',
            'type' => 'link',
            'source' => 'non-db',
            'relationship' => 'travelreceipts_travel',
            'module' => 'Travels',
            ],
        'receipt_date' => [
            'name' => 'receipt_date',
            'type' => 'date',
            'vname' => 'LBL_RECEIPT_DATE',
            ],
        'amount' => [
            'name' => 'amount',
            'type' => 'currency',
            'vname' => 'LBL_AMOUNT',
             ],
        'currency_id' => [
            'name' => 'currency_id',
            'type' => 'id',
            'vname' => 'LBL_CURRENCY',
            ],
        

    ],
    'relationships' => [
        'travelreceipts_travel' => [
            'lhs_module' => 'Travels',
            'lhs_table' => 'travels',
            'lhs_key' => 'id',

            'rhs_module' => 'TravelReceipts',
            'rhs_table' => 'travelreceipts',
            'rhs_key' => 'travel_id',

            'relationship_type' => 'one-to-many'
        ],
        'travelreceipts_costcenter' => [
            'lhs_module' => 'CostCenters',
            'lhs_table' => 'costcenters',
            'lhs_key' => 'id',

            'rhs_module' => 'TravelReceipts',
            'rhs_table' => 'travelreceipts',
            'rhs_key' => 'costcenter_id',

            'relationship_type' => 'one-to-many'
        ]
    ],

];

 VardefManager::createVardef('TravelReceipts', 'TravelReceipt', array('default', 'assignable'));
 //gewisse Standardfelder werden Automatisch generiert ID, Date, Name (default) bedeutet automatisch-generieren,
// assignible wird assigned Feld generieren
