<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['QuestionnaireEvaluation'] = [
    'table' => 'questionnaireevaluations',
    'fields' => [
        'questionnaireevaluationitems' => [
            'name'         => 'questionnaireevaluationitems',
            'vname'        => 'LBL_QUESTIONNAIRE_EVALUATION_ITEMS',
            'type'         => 'link',
            'module'       => 'QuestionnaireEvaluationItems',
            'relationship' => 'questionnaireevaluations_questionnaireevaluationitems',
            'link_type'    => 'many',
            'side'         => 'left',
            'source'       => 'non-db',
            'default'      => true
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
        ]
    ],
    'indices' => [
        [ 'name' => 'idx_questionnaireevaluationitem_del', 'type' => 'index', 'fields' => ['deleted'] ]
    ]
];

VardefManager::createVardef('QuestionnaireEvaluations', 'QuestionnaireEvaluation', ['default'] );
