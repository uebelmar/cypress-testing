<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['LandingPage'] = [

    'table' => 'landingpages',
    'audited' => true,

    'fields' => [
        'module_name' => [
            'name' => 'module_name',
            'type' => 'enum',
            'len' => 36,
            'options' => 'modules',
            'comment' => 'The module used for the landing page.',
            'vname' => 'LBL_MODULE'
        ],
        'active' => [
            'name' => 'active',
            'type' => 'bool',
            'vname' => 'LBL_ACTIVE',
            'default' => 0
        ],
        'content' => [
            'name' => 'content',
            'type' => 'html',
            'vname' => 'LBL_PAGE_CONTENT',
        ],
        'answer_content' => [
            'name' => 'answer_content',
            'type' => 'html',
            'required' => true,
            'vname' => 'LBL_ANSWER_CONTENT'
        ],
        'handlerclass' => [
            'name' => 'handlerclass',
            'type' => 'varchar',
            'len' => '255',
            'vname' => 'LBL_HANDLER_CLASS'
        ],
        'content_type' => [
            'name' => 'content_type',
            'vname' => 'LBL_CONTENT_TYPE',
            'type' => 'enum',
            'len' => 20,
            'default' => 'html',
            'required' => true,
            'options' => 'landingpage_content_type_dom'
        ],

    ],

    'indices' => [
        [
            'name' => 'idx_landingpages_name',
            'type' => 'index',
            'fields' => ['name']
        ],
        [
            'name' => 'idx_landingpages_module_name',
            'type' => 'index',
            'fields' => ['module_name']
        ],
        [
            'name' => 'idx_landingpages_deleted',
            'type' => 'index',
            'fields' => ['deleted']
        ],
        [
            'name' => 'idx_landingpages_active',
            'type' => 'index',
            'fields' => ['active']
        ],
    ],

    'relationships' => [],

    //This enables optimistic locking for Saves From EditView
    'optimistic_locking' => true,

];

VardefManager::createVardef('LandingPages', 'LandingPage', [ 'default', 'assignable' ]);
