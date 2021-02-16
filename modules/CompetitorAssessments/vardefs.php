<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['CompetitorAssessment'] = array(
    'table' => 'competitorassessments',
    'comment' => 'Competitor Assessments Module',
    'audited' =>  false,
    'duplicate_merge' =>  false,
    'unified_search' =>  false,
    //THIS FLAG ENABLES OPTIMISTIC LOCKING FOR SAVES FROM EDITVIEW
    'optimistic_locking'=>true,

    'fields' => array(
        'name' => array(
            'name' => 'name',
            'vname'  => 'LBL_COMPETITOR',
            'type'  => 'varchar',
            'len' => 150,
            'audited'  => false,
            'required'  => true,
            'comment'  => 'competitor name',
        ),
        'products' => array (
            'name' => 'products',
            'vname' => 'LBL_PRODUCTS',
            'type' => 'text',
            'required' => false,
            'massupdate' => false,
            'audited' => false,
        ),
        'differentitation' => array (
            'name' => 'differentitation',
            'vname' => 'LBL_DIFFERENTIATION',
            'type' => 'text',
            'required' => false,
            'massupdate' => false,
            'audited' => false,
        ),
        'weaknesses' => array (
            'name' => 'weaknesses',
            'vname' => 'LBL_WEAKNESSES',
            'type' => 'text',
            'required' => false,
            'massupdate' => false,
            'audited' => false,
        ),
        'strengths' => array (
            'name' => 'strengths',
            'vname' => 'LBL_STRENGTHS',
            'type' => 'text',
            'required' => false,
            'massupdate' => false,
            'audited' => false,
        ),
        'competitive_threat' => array (
            'name' => 'competitive_threat',
            'vname' => 'LBL_COMPETITIVE_THREAT',
            'type' => 'enum',
            'options' => 'competitive_threat_dom',
            'len' => 10,
            'required' => false,
            'massupdate' => false,
            'audited' => false,
        ),
        'competitive_status' => array (
            'name' => 'competitive_status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'options' => 'competitive_status_dom',
            'len' => 10,
            'required' => false,
            'massupdate' => false,
            'audited' => false,
        ),
        'opportunity_id' => array(
            'name' => 'opportunity_id',
            'vname'  => 'LBL_OPPORTUNITY_ID',
            'type'  => 'id',
            'audited'  => false,
            'required'  => true,
            'comment'  => '',
        ),
        'opportunity_name' => array(
            'name'=>'opportunity_name',
            'rname'=>'name',
            'vname' => 'LBL_OPPORTUNITY',
            'type' => 'relate',
            'reportable'=>false,
            'source'=>'non-db',
            'table' => 'opportunities',
            'id_name' => 'opportunity_id',
            'link' => 'opportunities',
            'module'=>'Opportunities',
            'duplicate_merge'=>'disabled',
            'comment' => 'Name of related opportunity',
        ),
        'opportunities' => array (
            'name' => 'opportunities',
            'vname'=>'LBL_OPPORTUNITIES',
            'type' => 'link',
            'relationship' => 'competitorassessments_opportunities',
            'link_type'=>'one',
            'side'=>'right',
            'source'=>'non-db',
        ),
    ),
    'indices' => array(
        array(
            'name' => 'idx_compass_opp',
            'type' => 'index',
            'fields' => array('opportunity_id'),
        ),
        array(
            'name' => 'idx_compass_oppdel',
            'type' => 'index',
            'fields' => array('opportunity_id', 'deleted'),
        ),
    ),
    'relationships' => array(
        'competitorassessments_opportunities' => array(
            'lhs_module' => 'Opportunities',
            'lhs_table' => 'opportunities',
            'lhs_key' => 'id',
            'rhs_module' => 'CompetitorAssessments',
            'rhs_table' => 'competitorassessments',
            'rhs_key' => 'opportunity_id',
            'relationship_type' => 'one-to-many',
        ),
    )
);

VardefManager::createVardef('CompetitorAssessments', 'CompetitorAssessment', array('default', 'assignable'));
