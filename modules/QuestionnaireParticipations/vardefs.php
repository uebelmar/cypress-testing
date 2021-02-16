<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['QuestionnaireParticipation'] = array(
    'table' => 'questionnaireparticipations',
    'fields' => [
        'parent_type' => array(
            'name'     => 'parent_type',
            'vname'    => 'LBL_PARENT_TYPE',
            'type'     => 'parent_type',
            'dbType'   => 'varchar',
            'required' => false,
            'len'      => 255,
            'comment'  => 'The Sugar object to which the questionnaire participation is related.',
        ),
        'parent_id' => array(
            'name' => 'parent_id',
            'vname' => 'LBL_LIST_RELATED_TO_ID',
            'type' => 'id',
            'reportable' => false,
            'comment'    => 'The ID of the parent Sugar object identified by parent_type.'
        ),
        'parent_name' => [
            'name'        => 'parent_name',
            'type_name'   => 'parent_type',
            'id_name'     => 'parent_id',
            'vname'       => 'LBL_RELATED_TO',
            'type'        => 'parent',
            'source'      => 'non-db',
        ],
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
        'questionnaire_id' => array(
            'name' => 'questionnaire_id',
            'vname' => 'LBL_QUESTIONNAIRE_ID',
            'type' => 'id'
        ),
        'questionnaire' => array (
            'name' => 'questionnaire',
            'vname' => 'LBL_QUESTIONNAIRE',
            'type' => 'link',
            'relationship' => 'questionnaire_questionnaireparticipations',
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
        'completed' => [
            'name'    => 'completed',
            'vname'   => 'LBL_COMPLETED',
            'type'    => 'bool',
            'default' => false,
        ],
        'contact_name' => [
            'name' => 'contact_name',
            'vname' => 'LBL_CONTACT',
            'type' => 'relate',
            'id_name' => 'contact_id',
            'rname' => 'full_name',
            'db_concat_fields' => array(0 => 'first_name', 1 => 'last_name'),
            'link' => 'contacts',
            'module' => 'Contacts',
            'bean_name' => 'Contact',
            'join_name' => 'contacts',
            'source' => 'non-db',
            'comment' => 'Name of related Contact'
        ],
        'contact_id' => [
            'name' => 'contact_id',
            'type' => 'varchar',
            'len' => 36,
            'rname' => 'id',
            'vname' => 'LBL_CONTACT_ID'
        ],
        'contact' => [
            'vname' => 'LBL_CONTACT',
            'name' => 'contact',
            'type' => 'link',
            'module' => 'Contacts',
            'relationship' => 'questionnaireparticipation_contact',
            'source' => 'non-db'
        ],
        'questionsetparticipations' => array (
            'name' => 'questionsetparticipations',
            'vname' => 'LBL_QUESTIONSETPARTICIPATIONS',
            'type' => 'link',
            'relationship' => 'questionnaireparticipation_questionsetparticipations',
            'source' => 'non-db',
            'module' => 'QuestionSetParticipations'
        )
    ],
    'relationships' => array(
        'questionnaireparticipation_contact' => [
            'lhs_module'                     => 'QuestionnaireParticipations',
            'lhs_table'                      => 'questionnaireparticipations',
            'lhs_key'                        => 'contact_id',
            'rhs_module'                     => 'Contacts',
            'rhs_table'                      => 'contacts',
            'rhs_key'                        => 'id',
            'relationship_type'              => 'one-to-one',
        ],
        'questionnaire_questionnaireparticipations' => [
            'lhs_module'                     => 'Questionnaires',
            'lhs_table'                      => 'questionnaires',
            'lhs_key'                        => 'id',
            'rhs_module'                     => 'QuestionnaireParticipations',
            'rhs_table'                      => 'questionnaireparticipations',
            'rhs_key'                        => 'questionnaire_id',
            'relationship_type'              => 'one-to-many',
        ],
    ),
    'indices' => [
        [ 'name' => 'idx_questionnaireparticipations_parent_type', 'type' => 'index', 'fields' => ['parent_type'] ],
        [ 'name' => 'idx_questionnaireparticipations_parent_id', 'type' => 'index', 'fields' => ['parent_id'] ],
        [ 'name' => 'idx_questionnaireparticipations_questionnaire_id', 'type' => 'index', 'fields' => ['questionnaire_id'] ],
        [ 'name' => 'idx_questionnaireparticipations_contact_id', 'type' => 'index', 'fields' => ['contact_id'] ],
        [ 'name' => 'idx_questionnaireparticipations_starttime', 'type' => 'index', 'fields' => ['starttime'] ],
        [ 'name' => 'idx_questionnaireparticipations_deleted', 'type' => 'index', 'fields' => ['deleted'] ]
    ]
);

VardefManager::createVardef('QuestionnaireParticipations', 'QuestionnaireParticipation', array('default','assignable'));

global $dictionary;
$dictionary['QuestionnaireParticipation']['fields']['name']['required'] = false;
