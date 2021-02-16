<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['Questionnaire'] = array(
    'table' => 'questionnaires',
    'fields' => array(
        'questionsets' => array(
            'name' => 'questionsets',
            'vname' => 'LBL_QUESTIONSETS',
            'type' => 'link',
            'relationship' => 'questionnaire_questionsets',
            'module' => 'QuestionSets',
            'source' => 'non-db',
            'deepClone' => true
        ),
        'questionnaireinterpretations' => array (
            'name' => 'questionnaireinterpretations',
            'vname' => 'LBL_QUESTIONNAIREINTERPRETATIONS',
            'type' => 'link',
            'relationship' => 'questionnaire_questionnaireinterpretations',
            'module' => 'QuestionnaireInterpretations',
            'source' => 'non-db'
        ),
        'textbefore' => array(
            'name' => 'textbefore',
            'type' => 'text',
            'vname' => 'LBL_TEXTBEFORE'
        ),
        'textafter' => array(
            'name' => 'textafter',
            'type' => 'text',
            'vname' => 'LBL_TEXTAFTER'
        ),
        'evaluationtype' => array(
            'name' => 'evaluationtype',
            'vname' => 'LBL_EVALUATIONTYPE',
            'type' => 'enum',
            'len' => 25,
            'options' => 'evaluationtypes_dom',
            'isnull' => false,
            'required' => true,
            'default' => 'default'
        ),
        'evaluationsorting' => array(
            'name' => 'evaluationsorting',
            'vname' => 'LBL_EVALUATIONSORTING',
            'type' => 'enum',
            'len' => 25,
            'options' => 'evaluationsorting_dom',
            'isnull' => false,
            'required' => true,
            'default' => 'categories'
        ),
        'time_limit' => array(
            'name' => 'time_limit',
            'vname' => 'LBL_TIME_LIMIT_MIN',
            'type' => 'int'
        ),
        'time_estimates' => array(
            'name' => 'time_estimates',
            'vname' => 'LBL_TIME_ESTIMATION_MIN',
            'type' => 'int'
        ),
        'fill_completely' => array(
            'name' => 'fill_completely',
            'vname' => 'LBL_TO_BE_FILLED_COMPLETELY',
            'type' => 'bool'
        ),
        'supconsultingorderitems' =>
            array(
                'name' => 'supconsultingorderitems',
                'type' => 'link',
                'relationship' => 'supconsultingorderitems_questionnaireinterpretations',
                'module' => 'SUPConsultingOrderItems',
                'bean_name' => 'SUPConsultingOrderItem',
                'source' => 'non-db',
                'vname' => 'LBL_SUPCONSULTINGORDERITEMS',
            ),
        'questionnaireparticipations' => [
            'name' => 'questionnaireparticipations',
            'vname' => 'LBL_QUESTIONNAIREPARTICIPATIONS',
            'type' => 'link',
            'relationship' => 'questionnaire_questionnaireparticipations',
            'module' => 'QuestionnaireParticipations',
            'source' => 'non-db'
        ],
        'categorypool' => [
            'name' => 'categorypool',
            'vname' => 'LBL_POSS_CATEGORIES',
            'type' => 'varchar',
            'len' => 1699 # 100 GUIDs a 16 Bytes + 99 commas
        ],
    ),
    'relationships' => array(
        'questionnaire_questionsets' => array(
            'lhs_module' => 'Questionnaires',
            'lhs_table' => 'questionnaires',
            'lhs_key' => 'id',
            'rhs_module' => 'QuestionSets',
            'rhs_table' => 'questionsets',
            'rhs_key' => 'questionnaire_id',
            'relationship_type' => 'one-to-many',
        ),
        'questionnaire_questionnaireinterpretations' => array(
            'lhs_module' => 'Questionnaires',
            'lhs_table' => 'questionnaires',
            'lhs_key' => 'id',
            'rhs_module' => 'QuestionnaireInterpretations',
            'rhs_table' => 'questionnaireinterpretations',
            'rhs_key' => 'questionnaire_id',
            'relationship_type' => 'one-to-many',
        ),
    ),
    'indices' => [
        [ 'name' => 'idx_questionnaires_deleted', 'type' => 'index', 'fields' => ['deleted'] ]
    ]
);

VardefManager::createVardef('Questionnaires', 'Questionnaire', array('default', 'assignable'));
