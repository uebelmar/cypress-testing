<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['Question'] = array(
    'table' => 'questions',
    'fields' => array(
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
            'relationship' => 'question_image',
            'source' => 'non-db',
            'module' => 'MediaFiles'
        ),
        'position' => array(
            'name' => 'position',
            'vname' => 'LBL_POSITION',
            'type' => 'uint',
            'isnull' => false,
            'required' => true
        ),
        'questionparameter' => array(
            'name' => 'questionparameter',
            'vname' => 'LBL_QUESTIONPARAMETER',
            'type' => 'text',
            'isnull' => true,
            'required' => false
        ),
        'questiontype' => array(
            'name' => 'questiontype',
            'vname' => 'LBL_QUESTIONTYPE',
            'type' => 'enum',
            'len' => 25,
            'options' => 'questionstypes_dom',
            'isnull' => false,
            'required' => true
        ),
        'questiontypeparameter' => array(
            'name' => 'questiontypeparameter',
            'vname' => 'LBL_QUESTIONTYPEPARAMETER',
            'type' => 'text'
        ),
        'questionset_id' => array(
            'name' => 'questionset_id',
            'vname' => 'LBL_QUESTIONSET_ID',
            'type' => 'id'
        ),
        'questionsets' => array (
            'name' => 'questionsets',
            'vname' => 'LBL_QUESTIONSET',
            'type' => 'link',
            'relationship' => 'questionset_questions',
            'source' => 'non-db',
            'module' => 'QuestionSets'
        ),
        'questionset_name' => array(
            'name' => 'questionset_name',
            'rname' => 'name',
            'id_name' => 'questionset_id',
            'vname' => 'LBL_QUESTIONSET',
            'join_name' => 'questionset',
            'type' => 'relate',
            'link' => 'questionsets',
            'table' => 'questionsets',
            'isnull' => 'true',
            'module' => 'QuestionSets',
            'dbType' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
        ),
        'questionoptions' => array (
            'name' => 'questionoptions',
            'vname' => 'LBL_QUESTIONOPTIONS',
            'type' => 'link',
            'relationship' => 'question_questionoptions',
            'source' => 'non-db',
            'module' => 'QuestionOptions',
            'default' => true,
            'deepClone' => true
        ),
    ),
    'relationships' => array(
        'question_questionoptions' => array(
            'lhs_module' => 'Questions',
            'lhs_table' => 'questions',
            'lhs_key' => 'id',
            'rhs_module' => 'QuestionOptions',
            'rhs_table' => 'questionoptions',
            'rhs_key' => 'question_id',
            'relationship_type' => 'one-to-many',
        ),
        'question_questionanswers' => array(
            'lhs_module' => 'Questions',
            'lhs_table' => 'questions',
            'lhs_key' => 'id',
            'rhs_module' => 'QuestionAnswers',
            'rhs_table' => 'questionanswers',
            'rhs_key' => 'question_id',
            'relationship_type' => 'one-to-many',
        ),
        'question_image' => array(
            'lhs_module' => 'Questions',
            'lhs_table' => 'questions',
            'lhs_key' => 'image_id',
            'rhs_module' => 'MediaFiles',
            'rhs_table' => 'mediafiles',
            'rhs_key' => 'id',
            'relationship_type' => 'one-to-one',
        )
    ),
    'indices' => [
        [ 'name' => 'idx_questions_position', 'type' => 'index', 'fields' => ['position'] ],
        [ 'name' => 'idx_questions_dateentered', 'type' => 'index', 'fields' => ['date_entered'] ],
        [ 'name' => 'idx_questions_deleted', 'type' => 'index', 'fields' => ['deleted'] ],
        [ 'name' => 'idx__questions__questionset_id__deleted', 'type' => 'index', 'fields' => ['questionset_id','deleted'] ],
        [ 'name' => 'idx_questions_image_id', 'type' => 'index', 'fields' => ['image_id'] ],
    ],
);

VardefManager::createVardef('Questions', 'Question', array('default','assignable'));
