<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['QuestionnaireEvaluationItem'] = [
    'table' => 'questionnaireevaluationitems',
    'fields' => [
        'id' => array(
            'name' => 'id',
            'type' => 'id'
        ),
        'name' => [
            'name'     => 'name',
            'vname'    => 'LBL_NAME',
            'type'     => 'varchar',
            'len'      => 255,
            'required' => true,
            'isnull'   => false
        ],
        'value' => [
            'name' => 'value',
            'type' => 'float',
            'vname' => 'LBL_VALUE'
        ],
        'deleted' => [
            'name'     => 'deleted',
            'vname'    => 'LBL_DELETED',
            'type'     => 'bool',
            'default' => '0'
        ],
        'questionnaireevaluation_id' => [
            'name'       => 'questionnaireevaluation_id',
            'vname'      => 'LBL_QUESTIONNAIRE_EVALUATION_ID',
            'type'       => 'id',
            'required'   => true,
            'isnull'     => false
        ],
        'questionnaireevaluation_name' => [
            'name'             => 'questionnaireevaluation_name',
            'vname'            => 'LBL_QUESTIONNAIRE_EVALUATION',
            'type'             => 'relate',
            'source'           => 'non-db',
            'len'              => 255,
            'id_name'          => 'questionnaireevaluation_id',
            'rname'            => 'name',
            'module'           => 'QuestionnaireEvaluations',
            'link'             => 'questionnaireevaluations',
            'join_name'        => 'questionnaireevaluations',
        ],
        'questionnaireevaluations' => [
            'name'         => 'questionnaireevaluation',
            'vname'        => 'LBL_QUESTIONNAIRE_EVALUATION',
            'type'         => 'link',
            'module'       => 'QuestionnaireEvaluations',
            'relationship' => 'questionnaireevaluations_questionnaireevaluationitems',
            'link_type'    => 'many',
            'side'         => 'right',
            'source'       => 'non-db'
        ],
        'questionoptioncategories' => [
            'name'         => 'questionoptioncategories',
            'vname'        => 'LBL_QUESTION_OPTION_CATEGORY',
            'type'         => 'link',
            'module'       => 'QuestionOptionCategories',
            'relationship' => 'questionnaireevaluationitem_questionoptioncategory',
            'link_type'    => 'many',
            'source'       => 'non-db'
        ]
    ],
    'relationships' => [
        'questionnaireevaluations_questionnaireevaluationitems' => [
            'lhs_module'        => 'QuestionnaireEvaluations',
            'lhs_table'         => 'questionnaireevaluations',
            'lhs_key'           => 'id',
            'rhs_module'        => 'QuestionnaireEvaluationItems',
            'rhs_table'         => 'questionnaireevaluationitems',
            'rhs_key'           => 'questionnaireevaluation_id',
            'relationship_type' => 'one-to-many',
        ],
        'questionnaireevaluationitem_questionoptioncategory' => [
            'lhs_module'        => 'QuestionOptionCategories',
            'lhs_table'         => 'questionoptioncategories',
            'lhs_key'           => 'abbreviation',
            'rhs_module'        => 'QuestionnaireEvaluationItems',
            'rhs_table'         => 'questionnaireevaluationitems',
            'rhs_key'           => 'name',
            'relationship_type' => 'one-to-many',
        ]
    ],
    'indices' => [
        [ 'name' => 'idx_questionnaireevaluationitem_name', 'type' => 'index', 'fields' => ['name'] ],
        [ 'name' => 'idx_questionnaireevaluationitem_qsteval', 'type' => 'index', 'fields' => ['questionnaireevaluation_id'] ],
        [ 'name' => 'idx_questionnaireevaluationitem_del', 'type' => 'index', 'fields' => ['deleted'] ],
    ]
];

VardefManager::createVardef('QuestionnaireEvaluationItems', 'QuestionnaireEvaluationItem', [] );
