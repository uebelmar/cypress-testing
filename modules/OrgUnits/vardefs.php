<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['OrgUnit'] = array(
    'table' => 'orgunits',
    'comment' => 'OrgUnits Module',
    'audited' => false,
    'duplicate_merge' => false,
    'unified_search' => false,

    'fields' => array(
        'parent_id' => array(
            'name' => 'parent_id',
            'vname' => 'LBL_PARENT_ID',
            'type' => 'id',
            'required' => false,
            'reportable' => false,
            'audited' => true,
            'comment' => 'ID of the parent of this Unit',
        ),
        'parent_name' => array(
            'name' => 'parent_name',
            'rname' => 'name',
            'id_name' => 'parent_id',
            'vname' => 'LBL_MEMBER_OF',
            'type' => 'relate',
            'isnull' => 'true',
            'module' => 'OrgUnits',
            'table' => 'orgunits',
            'massupdate' => false,
            'source' => 'non-db',
            'len' => 36,
            'link' => 'member_of',
            'unified_search' => true,
            'importable' => 'true',
        ),
        'members' =>
            array(
                'name' => 'members',
                'type' => 'link',
                'relationship' => 'member_orgunits',
                'module' => 'OrgUnits',
                'bean_name' => 'OrgUnit',
                'source' => 'non-db',
                'vname' => 'LBL_MEMBERS',
            ),
        'member_of' =>
            array(
                'name' => 'member_of',
                'type' => 'link',
                'relationship' => 'member_orgunits',
                'module' => 'OrgUnits',
                'bean_name' => 'OrgUnit',
                'link_type' => 'one',
                'source' => 'non-db',
                'vname' => 'LBL_MEMBER_OF',
                'side' => 'right',
            ),
    ),
    'relationships' => array(
        'member_orgunits' => array(
            'lhs_module' => 'OrgUnits', 'lhs_table' => 'orgunits', 'lhs_key' => 'id',
            'rhs_module' => 'OrgUnits', 'rhs_table' => 'orgunits', 'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many'
        ),
    ),
    'indices' => array(
        array('name' => 'idx_orgunit_id_del', 'type' => 'index', 'fields' => array('id', 'deleted')),
        array('name' => 'idx_orgunit_parent_id', 'type' => 'index', 'fields' => array('parent_id')),
    )
);

VardefManager::createVardef('OrgUnits', 'OrgUnit', array('default', 'assignable'));
