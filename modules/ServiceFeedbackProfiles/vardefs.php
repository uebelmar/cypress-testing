<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ServiceFeedbackProfile'] = [
    'table'           => 'servicefeedbackprofiles',
    'comment'         => 'ServiceFeedbackProfiles Module',
    'audited'         =>  false,
    'duplicate_merge' =>  false,
    'unified_search'  =>  false,

    'fields' => [
        'percentage' => [
            'name'   => 'percentage',
            'vname'  => 'LBL_PERCENTAGE',
            'type'   => 'varchar',
            'dbType' => 'int',
            'len'    => 3,
        ],

        //questionnaire
        'questionnaire_id' => [
            'name'       => 'questionnaire_id',
            'vname'      => 'LBL_QUESTIONNAIRE_ID',
            'type'       => 'id',
            'reportable' => true,
            'comment'    => 'The ID of the questionnaire',
        ],
        'questionnaire_name' => [
            'name'             => 'questionnaire_name',
            'vname'            => 'LBL_QUESTIONNAIRE',
            'type'             => 'relate',
            'source'           => 'non-db',
            'len'              => '255',
            'id_name'          => 'questionnaire_id',
            'rname'            => 'name',
            'module'           => 'Questionnaires',
            'link'             => 'questionnaires',
            'join_name'        => 'questionnaires',
        ],
        'questionnaires' => [
            'vname'        => 'LBL_QUESTIONNAIRES',
            'name'         => 'questionnaires',
            'type'         => 'link',
            'module'       => 'Questionnaires',
            'relationship' => 'servicefeedbackprofiles_questionnaires',
            'link_type'    => 'one',
            'side'         => 'right',
            'source'       => 'non-db',
        ],
    ],

    'relationships' => [
        'servicefeedbackprofiles_questionnaires' => [
            'lhs_module'        => 'Questionnaires',
            'lhs_table'         => 'questionnaires',
            'lhs_key'           => 'id',
            'rhs_module'        => 'ServiceFeedbackProfiles',
            'rhs_table'         => 'servicefeedbackprofiles',
            'rhs_key'           => 'questionnaire_id',
            'relationship_type' => 'one-to-many',
        ],
    ],

    'indices' => [
        ['name' => 'idx_servicefeedbackprofile_questionnaire', 'type' => 'index', 'fields' => ['questionnaire_id']],
    ],
];

VardefManager::createVardef('ServiceFeedbackProfiles', 'ServiceFeedbackProfile', ['default', 'assignable']);
