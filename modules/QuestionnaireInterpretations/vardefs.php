<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['QuestionnaireInterpretation'] = array(
    'table' => 'questionnaireinterpretations',
    'fields' => array(
        'text_short' => array(
            'name' => 'text_short',
            'type' => 'text',
            'vname' => 'LBL_TEXT_SHORT',
            'stylesheet_id_field' => 'text_short_stylesheet_id',
        ),
        'text_short_stylesheet_id' => array(
            'name' => 'text_short_stylesheet_id',
            'type' => 'char',
            'len' => 36
        ),
        'text_long' => array(
            'name' => 'text_long',
            'type' => 'text',
            'vname' => 'LBL_TEXT_LONG',
            'stylesheet_id_field' => 'text_long_stylesheet_id',
        ),
        'text_long_stylesheet_id' => array(
            'name' => 'text_long_stylesheet_id',
            'type' => 'char',
            'len' => 36
        ),
        'questionnaire_id' => array(
            'name' => 'questionnaire_id',
            'vname' => 'LBL_QUESTIONNAIRE_ID',
            'type' => 'id'
        ),
        'questionnaires' => array (
            'name' => 'questionnaires',
            'vname' => 'LBL_QUESTIONNAIRES',
            'type' => 'link',
            'relationship' => 'questionnaire_questionnaireinterpretations',
            'source' => 'non-db',
            'module' => 'Questionnaires'
        ),
        'questionnaire_name' => array(
            'name' => 'questionnaire_name',
            'rname' => 'name',
            'id_name' => 'questionnaire_id',
            'vname' => 'LBL_QUESTIONNAIRE',
            'join_name' => 'questionnaire',
            'type' => 'relate',
            'link' => 'questionnaires',
            'table' => 'questionnaires',
            'isnull' => 'true',
            'module' => 'Questionnaires',
            'dbType' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
        ),
        'categories' => array(
            'name' => 'categories',
            'vname' => 'LBL_CATEGORIES',
            'type' => 'text'
        ),
        'text_extra' => array(
            'name' => 'text_extra',
            'type' => 'text',
            'source' => 'non-db',
            'vname' => 'LBL_TEXT_EXTRA'
        )
    ),
    'relationships' => array(),
    'indices' => [
        [ 'name' => 'idx_questionnaireinterpretations_questionnaire_id', 'type' => 'index', 'fields' => ['questionnaire_id'] ],
        [ 'name' => 'idx_questionnaireinterpretations_deleted', 'type' => 'index', 'fields' => ['deleted'] ],
        [ 'name' => 'idx_questionnaireinterpretations_text_short_stylesheet_id', 'type' => 'index', 'fields' => ['text_short_stylesheet_id'] ],
        [ 'name' => 'idx_questionnaireinterpretations_text_long_stylesheet_id', 'type' => 'index', 'fields' => ['text_long_stylesheet_id'] ]
    ],
);

VardefManager::createVardef('QuestionnaireInterpretations', 'QuestionnaireInterpretation', array('default','assignable') );
