<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['QuestionOptionCategory'] = array(
    'table' => 'questionoptioncategories',
    'fields' => array(
        'abbreviation' => array(
            'name' => 'abbreviation',
            'vname' => 'LBL_ABBREVIATION',
            'type' => 'varchar',
            'len' => 255,
            'required' => true
        ),
        'sortkey' => array(
            'name' => 'sortkey',
            'vname' => 'LBL_SORTKEY',
            'type' => 'varchar',
            'len' => 5
        ),
        'questionnaireevaluationitems' => [
            'name'         => 'questionnaireevaluationitems',
            'vname'        => 'LBL_QUESTIONNAIRE_EVALUATION_ITEMS',
            'type'         => 'link',
            'module'       => 'QuestionnaireEvaluationItems',
            'relationship' => 'questionnaireevaluationitem_questionoptioncategory',
            'link_type'    => 'many',
            'source'       => 'non-db'
        ]
    ),
    'relationships' => array(),
    'indices' => [
        [ 'name' => 'idx_questionoptioncategories_name', 'type' => 'index', 'fields' => ['name'] ],
        [ 'name' => 'idx_questionoptioncategories_abbreviation', 'type' => 'index', 'fields' => ['abbreviation'] ],
        [ 'name' => 'idx_questionoptioncategories_deleted', 'type' => 'index', 'fields' => ['deleted'] ],
        [ 'name' => 'idx_questionoptioncategories_sortkey', 'type' => 'index', 'fields' => ['sortkey'] ]
    ]
);

VardefManager::createVardef('QuestionOptionCategories', 'QuestionOptionCategory', array('default','assignable') );
