<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['QuestionAnswer'] = array(
    'table' => 'questionanswers',
    'fields' => array(
       'optionlessAnswerValue' => array(
            'name' => 'optionlessAnswerValue',
            'vname' => 'LBL_TEXTINPUT',
            'type' => 'varchar',
            'len' => '10000'
        ),
        'question_id' => array(
            'name' => 'question_id',
            'vname' => 'LBL_QUESTION_ID',
            'type' => 'id'
        ),
        'question' => array (
            'name' => 'question',
            'vname' => 'LBL_QUESTION',
            'type' => 'link',
            'relationship' => 'question_questionanswers',
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
            'link' => 'question',
            'table' => 'questions',
            'isnull' => 'true',
            'module' => 'Questions',
            'dbType' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
        ),
        'question_position' => array(
            'name' => 'question_position',
            'rname' => 'position',
            'id_name' => 'question_id',
            'vname' => 'LBL_POSITION',
            'join_name' => 'question',
            'type' => 'relate',
            'link' => 'question',
            'table' => 'questions',
            'module' => 'Questions',
            'dbType' => 'uint',
            'source' => 'non-db'
        ),
        'questionoption_id' => array(
            'name' => 'questionoption_id',
            'vname' => 'LBL_QUESTIONOPTION_ID',
            'type' => 'id'
        ),
        'questionoption' => array (
            'name' => 'questionoption',
            'vname' => 'LBL_QUESTIONOPTION',
            'type' => 'link',
            'relationship' => 'questionoption_questionanswers',
            'source' => 'non-db',
            'module' => 'QuestionOptions'
        ),
        'questionoption_text' => array(
            'name' => 'questionoption_text',
            'rname' => 'text',
            'id_name' => 'questionoption_id',
            'vname' => 'LBL_QUESTIONOPTION_TEXT',
            'join_name' => 'questionoption',
            'type' => 'relate',
            'link' => 'questionoptions',
            'table' => 'questionoptions',
            'isnull' => 'true',
            'module' => 'QuestionOptions',
            'dbType' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
        ),
        'questionsetparticipation_id' => array(
            'name' => 'questionsetparticipation_id',
            'vname' => 'LBL_QUESTIONSETPARTICIPATION_ID',
            'type' => 'id'
        ),
        'questionsetparticipation' => array (
            'name' => 'questionsetparticipation',
            'vname' => 'LBL_QUESTIONSETPARTICIPATION',
            'type' => 'link',
            'relationship' => 'questionsetparticipation_questionanswers',
            'source' => 'non-db',
            'module' => 'QuestionSetParticipations'
        ),
        'questionsetparticipation_name' => array(
            'name' => 'questionsetparticipation_name',
            'rname' => 'name',
            'id_name' => 'questionsetparticipation_id',
            'vname' => 'LBL_QUESTIONSETPARTICIPATION',
            'join_name' => 'questionsetparticipation',
            'type' => 'relate',
            'link' => 'questionsetparticipations',
            'table' => 'questionsetparticipations',
            'isnull' => 'true',
            'module' => 'QuestionSetParticipations',
            'dbType' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
        )
    ),
    'relationships' => array(),
    'indices' => [
        [ 'name' => 'idx_questionanswers_question_id_del', 'type' => 'index', 'fields' => ['question_id','deleted'] ],
        [ 'name' => 'idx_questionanswers_questionoption_id', 'type' => 'index', 'fields' => ['questionoption_id','deleted'] ],
        [ 'name' => 'idx_questionanswers_questionsetparticipation_id', 'type' => 'index', 'fields' => ['questionsetparticipation_id','deleted'] ],
        [ 'name' => 'idx_questionanswers_deleted', 'type' => 'index', 'fields' => ['deleted'] ]
    ],
);

VardefManager::createVardef('QuestionAnswers', 'QuestionAnswer', array('default') );
