<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['QuestionOption'] = array(
    'table' => 'questionoptions',
    'fields' => array(
        'answer' => array(
            'name' => 'answer',
            'vname' => 'LBL_ANSWER',
            'type' => 'varchar',
            'len' => 100
        ),
        'image_id' => array(
            'name' => 'image_id',
            'vname' => 'LBL_IMAGE_ID',
            'type' => 'mediafile',
            'dbType' => 'varchar'
        ),
        'image' => array (
            'name' => 'image',
            'vname' => 'LBL_IMAGE',
            'type' => 'link',
            'relationship' => 'questionoption_image',
            'source' => 'non-db',
            'module' => 'MediaFiles'
        ),
        'question_id' => array(
            'name' => 'question_id',
            'vname' => 'LBL_QUESTION_ID',
            'type' => 'id'
        ),
        'questions' => array (
            'name' => 'questions',
            'vname' => 'LBL_QUESTION',
            'type' => 'link',
            'relationship' => 'question_questionoptions',
            'source' => 'non-db',
            'module' => 'Questions'
        ),
        'question_name' => array(
            'name' => 'question_name',
            'rname' => 'name',
            'id_name' => 'question_id',
            'vname' => 'LBL_QUESTION',
            'join_name' => 'question',
            'type' => 'relate',
            'link' => 'questions',
            'table' => 'questions',
            'isnull' => true,
            'module' => 'Questions',
            'dbType' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
        ),
        'answer' => array(
            'name' => 'answer',
            'vname' => 'LBL_ANSWER',
            'type' => 'varchar',
            'len' => 50
        ),
        'categories' => array(
            'name' => 'categories',
            'vname' => 'LBL_CATEGORIES',
            'type' => 'text'
        ),
        'points' => array(
            'name' => 'points',
            'vname' => 'LBL_POINTS',
            'type' => 'double'
        ),
        'questionset_type_parameter_id' => array(
            'name' => 'questionset_type_parameter_id',
            'vname' => 'LBL_QUESTIONSET_TYPE_PARAMETER_ID',
            'type' => 'id'
        ),
        'position' => array(
            'name' => 'position',
            'vname' => 'LBL_POSITION',
            'type' => 'uint',
            'isnull' => false,
            'required' => true
        ),
        'is_correct_option' => array(
            'name' => 'is_correct_option',
            'vname' => 'LBL_IS_CORRECT_OPTION',
            'type' => 'bool'
        ),
        'text' => array(
            'name' => 'text',
            'vname' => 'LBL_TEXT',
            'type' => 'text'
        )
    ),
    'relationships' => array(
        'questionoption_questionanswers' => array(
            'lhs_module' => 'QuestionOptions',
            'lhs_table' => 'questionoptions',
            'lhs_key' => 'id',
            'rhs_module' => 'QuestionAnswers',
            'rhs_table' => 'questionanswers',
            'rhs_key' => 'questionoption_id',
            'relationship_type' => 'one-to-many'
        ),
        'questionoption_image' => array(
            'lhs_module' => 'QuestionOptions',
            'lhs_table' => 'questionoptions',
            'lhs_key' => 'image_id',
            'rhs_module' => 'MediaFiles',
            'rhs_table' => 'mediafiles',
            'rhs_key' => 'id',
            'relationship_type' => 'one-to-one',
        )
    ),
    'indices' => [
        [ 'name' => 'idx_questionoptions_position', 'type' => 'index', 'fields' => ['position'] ],
        [ 'name' => 'idx_questionoptions_deleted', 'type' => 'index', 'fields' => ['deleted'] ],
        [ 'name' => 'idx__questionoptions__question_id__deleted', 'type' => 'index', 'fields' => ['question_id','deleted'] ],
        [ 'name' => 'idx_questionoptions_image_id', 'type' => 'index', 'fields' => ['image_id'] ]
    ],
);

VardefManager::createVardef('QuestionOptions', 'QuestionOption', array('default','assignable'));
