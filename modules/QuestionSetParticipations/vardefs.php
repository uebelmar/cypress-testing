<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['QuestionSetParticipation'] = array(
    'table' => 'questionsetparticipations',
    'fields' => array(
        # 'name' => ?!?!?!
        'referencetype' => array(
            'name' => 'referencetype',
            'vname' => 'LBL_REFERENCETYPE',
            'type' => 'varchar',
            'length' => '50',
            'isnull' => false,
            'required' => true
        ),
        'referenceid' => array(
            'name' => 'referenceid',
            'vname' => 'LBL_REFERENCEID',
            'type' => 'id'
        ),
        'starttime' => array(
            'name' => 'starttime',
            'vname' => 'LBL_STARTTIME',
            'type' => 'datetime'
        ),
        'endtime' => array(
            'name' => 'endtime',
            'vname' => 'LBL_ENDTIME',
            'type' => 'datetime'
        ),
        'questionset_id' => array(
            'name' => 'questionset_id',
            'vname' => 'LBL_QUESTIONSET_ID',
            'type' => 'id'
        ),
        'questionset' => array (
            'name' => 'questionset',
            'vname' => 'LBL_QUESTIONSET',
            'type' => 'link',
            'relationship' => 'questionset_questionsetparticipations',
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
        'questionnaireparticipation_id' => array(
            'name' => 'questionnaireparticipation_id',
            'vname' => 'LBL_QUESTIONNAIREPARTICIPATION_ID',
            'type' => 'id'
        ),
        'questionnaireparticipation' => array (
            'name' => 'questionnaireparticipation',
            'vname' => 'LBL_QUESTIONNAIREPARTICIPATION',
            'type' => 'link',
            'relationship' => 'questionnaireparticipation_questionsetparticipations',
            'source' => 'non-db',
            'module' => 'QuestionnaireParticipations'
        ),
        'questionanswers' => array (
            'name' => 'questionanswers',
            'vname' => 'LBL_QUESTIONANSWERS',
            'type' => 'link',
            'relationship' => 'questionsetparticipation_questionanswers',
            'source' => 'non-db',
            'module' => 'QuestionAnswers'
        )
    ),
    'relationships' => array(
        'questionsetparticipation_questionanswers' => array(
            'lhs_module' => 'QuestionSetParticipations',
            'lhs_table' => 'questionsetparticipations',
            'lhs_key' => 'id',
            'rhs_module' => 'QuestionAnswers',
            'rhs_table' => 'questionanswers',
            'rhs_key' => 'questionsetparticipation_id',
            'relationship_type' => 'one-to-many',
        ),
        'questionnaireparticipation_questionsetparticipations' => array(
            'lhs_module' => 'QuestionnaireParticipations',
            'lhs_table' => 'questionnaireparticipations',
            'lhs_key' => 'id',
            'rhs_module' => 'QuestionSetParticipations',
            'rhs_table' => 'questionsetparticipations',
            'rhs_key' => 'questionnaireparticipation_id',
            'relationship_type' => 'one-to-many',
        ),
    ),
    'indices' => [
        [ 'name' => 'idx_questionsetparticipations_referencetype', 'type' => 'index', 'fields' => ['referencetype'] ],
        [ 'name' => 'idx_questionsetparticipations_referenceid', 'type' => 'index', 'fields' => ['referenceid'] ],
        [ 'name' => 'idx_questionsetparticipations_questionset_id', 'type' => 'index', 'fields' => ['questionset_id'] ],
        [ 'name' => 'idx_questionsetparticipations_starttime', 'type' => 'index', 'fields' => ['starttime'] ],
        [ 'name' => 'idx_questionsetparticipations_deleted', 'type' => 'index', 'fields' => ['deleted'] ],
        [ 'name' => 'idx_questionsetparticipations_questionnaireparticipation_id', 'type' => 'index', 'fields' => ['questionnaireparticipation_id'] ]
    ]
);

VardefManager::createVardef('QuestionSetParticipations', 'QuestionSetParticipation', array('default','assignable'));
