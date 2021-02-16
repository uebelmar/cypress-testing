<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['QuestionSet'] = array(
    'table' => 'questionsets',
    'fields' => array(
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
            # ,'isnull' => false   ?
            # ,'required' => true   ?
        ),
        'position' => array(
            'name' => 'position',
            'vname' => 'LBL_POSITION_IN_QUESTIONNAIRE',
            'type' => 'uint',
            'isnull' => false,
            'required' => true
        ),
        'shuffle' => array(
            'name' => 'shuffle',
            'vname' => 'LBL_SHUFFLE_QUESTIONS',
            'type' => 'bool'
        ),
        'timelimit' => array(
            'name' => 'timelimit',
            'vname' => 'LBL_TIMELIMIT_SEC',
            'type' => 'uint',
            'default' => 0
        ),
        'textbefore' => array(
            'name' => 'textbefore',
            'vname' => 'LBL_PROLOGUE',
            'type' => 'text',
            'isnull' => true,
            'required' => false
        ),
        'textafter' => array(
            'name' => 'textafter',
            'vname' => 'LBL_EPILOGUE',
            'type' => 'text',
            'isnull' => true,
            'required' => false
        ),
        /*
        'categorypool' => array(
            'name' => 'categorypool',
            'vname' => 'LBL_POSS_CATEGORIES',
            'type' => 'varchar',
            'len' => 1699 # 100 GUIDs a 16 Bytes + 99 commas
        ),
        */
        'questionnaire_id' => array(
            'name' => 'questionnaire_id',
            'vname' => 'LBL_QUESTIONNAIRE_ID',
            'type' => 'id',
        ),
        'showonlyimages' => array(
            'name' => 'showonlyimages',
            'vname' => 'LBL_SHOW_ONLY_IMAGES',
            'type' => 'bool'
        ),
        'questionnaires' => array (
            'name' => 'questionnaires',
            'vname' => 'LBL_QUESTIONNAIRE',
            'type' => 'link',
            'relationship' => 'questionnaire_questionsets',
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
        'questions' => array(
            'name' => 'questions',
            'vname' => 'LBL_QUESTIONS',
            'type' => 'link',
            'relationship' => 'questionset_questions',
            'source' => 'non-db',
            'default' => true,
            'module' => 'Questions',
            'deepClone' => true
        ),
    ),
    'relationships' => array(
        'questionset_questions' => array(
            'lhs_module' => 'QuestionSets',
            'lhs_table' => 'questionsets',
            'lhs_key' => 'id',
            'rhs_module' => 'Questions',
            'rhs_table' => 'questions',
            'rhs_key' => 'questionset_id',
            'relationship_type' => 'one-to-many',
        ),
        'questionset_questionsetparticipations' => array(
            'lhs_module' => 'QuestionSets',
            'lhs_table' => 'questionsets',
            'lhs_key' => 'id',
            'rhs_module' => 'QuestionSetParticipations',
            'rhs_table' => 'questionsetparticipations',
            'rhs_key' => 'questionset_id',
            'relationship_type' => 'one-to-many',
        ),
    ),
    'indices' => [
        [ 'name' => 'idx_questionsets_position', 'type' => 'index', 'fields' => ['position'] ],
        [ 'name' => 'idx__questionsets__questionnaire_id__deleted', 'type' => 'index', 'fields' => ['questionnaire_id','deleted'] ],
        [ 'name' => 'idx_questionsets_deleted', 'type' => 'index', 'fields' => ['deleted'] ]
    ],
);

VardefManager::createVardef('QuestionSets', 'QuestionSet', array('default','assignable'));
