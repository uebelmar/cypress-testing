<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['OpportunityStage'] = array(
    'table' => 'opportunitystages',
    'comment' => 'track opportunity stage changes',
    'fields' => array(
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'name',
            'dbType' => 'varchar',
            'len' => '50',
            'required' => false
        ),
        'amount' => array(
            'name' => 'amount',
            'vname' => 'LBL_AMOUNT',
            //'function'=>array('vname'=>'getCurrencyType'),
            'type' => 'currency',
            'dbType' => 'double',
            'comment' => 'Unconverted amount of the opportunity',
            'importable' => 'required',
            'duplicate_merge' => '1',
            'required' => true,
            'options' => 'numeric_range_search_dom',
            'enable_range_search' => true,
        ),
        'amount_usdollar' => array(
            'name' => 'amount_usdollar',
            'vname' => 'LBL_AMOUNT_USDOLLAR',
            'type' => 'currency',
            'group' => 'amount',
            'dbType' => 'double',
            'disable_num_format' => true,
            'audited' => true
        ),
        'forecast' => array(
            'name' => 'forecast',
            'vname' => 'LBL_FORECAST',
            'type' => 'bool'
        ),
        'budget' => array(
            'name' => 'budget',
            'vname' => 'LBL_BUDGET',
            'type' => 'currency',
            'dbType' => 'double',
        ),
        'bestcase' => array(
            'name' => 'bestcase',
            'vname' => 'LBL_BESTCASE',
            'type' => 'currency',
            'dbType' => 'double',
        ),
        'worstcase' => array(
            'name' => 'worstcase',
            'vname' => 'LBL_WORSTCASE',
            'type' => 'currency',
            'dbType' => 'double',
        ),
        'currency_id' => array(
            'name' => 'currency_id',
            'type' => 'id',
            'group' => 'currency_id',
            'vname' => 'LBL_CURRENCY',
        ),
        'date_closed' => array(
            'name' => 'date_closed',
            'vname' => 'LBL_DATE_CLOSED',
            'type' => 'date',
            'audited' => true
        ),
        'sales_stage' => array(
            'name' => 'sales_stage',
            'vname' => 'LBL_SALES_STAGE',
            'type' => 'enum',
            'options' => 'sales_stage_dom',
            'len' => '255',
            'audited' => true,
        ),
        'probability' => array(
            'name' => 'probability',
            'vname' => 'LBL_PROBABILITY',
            'type' => 'int',
            'dbtype' => 'double',
            'audited' => true,
        ),
        'opportunity_id' => array(
            'name' => 'opportunity_id',
            'type' => 'varchar',
            'len' => 36
        ),
        'opportunities' => array(
            'name' => 'opportunities',
            'type' => 'link',
            'relationship' => 'opportunity_opportunitystages',
            'source' => 'non-db',
            'link_type' => 'one',
            'module' => 'Opportunities',
            'bean_name' => 'Opportunity',
            'vname' => 'LBL_OPPORTUNITY',
        ),
    ),
    'indices' => array(
        array(
            'name' => 'idx_opp_id',
            'type' => 'index',
            'fields' => array('opportunity_id'),
        )
    ),
    'relationships' => array(
        'opportunity_opportunitystages' => array(
            'lhs_module' => 'Opportunities',
            'lhs_table' => 'opportunities',
            'lhs_key' => 'id',
            'rhs_module' => 'OpportunityStages',
            'rhs_table' => 'opportunitystages',
            'rhs_key' => 'opportunity_id',
            'relationship_type' => 'one-to-many',
        )
    )
    //This enables optimistic locking for Saves From EditView
, 'optimistic_locking' => true,
);
VardefManager::createVardef('OpportunityStages', 'OpportunityStage', array('default', 'assignable',
));
