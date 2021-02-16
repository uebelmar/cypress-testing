<?php


use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['ServiceCall'] = array(
    'table' => 'servicecalls',
    'comment' => 'ServiceCalls Module',
    'audited' => false,
    'duplicate_merge' => false,
    'unified_search' => false,

    'fields' => array(
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_TOPIC',
            'type' => 'varchar',
            'len' => 100,
            'requied' => false

        ),
        'servicecall_type' => array(
            'name' => 'servicecall_type',
            'vname' => 'LBL_TYPE',
            'type' => 'enum',
            'len' => 25,
            'options' => 'servicecall_type_dom'
        ),
        'date_start' => array(
            'name' => 'date_start',
            'vname' => 'LBL_DATE',
            'type' => 'datetimecombo',
            'dbType' => 'datetime',
            'comment' => 'Date in which call is schedule to (or did) start',
            'importable' => 'required',
            'required' => true,
            'enable_range_search' => true,
            'options' => 'date_range_search_dom',
        ),
        'date_end' => array(
            'name' => 'date_end',
            'vname' => 'LBL_DATE_END',
            'type' => 'datetimecombo',
            'dbType' => 'datetime',
            'massupdate' => false,
            'comment' => 'Date is which call is scheduled to (or did) end',
            'enable_range_search' => true,
            'options' => 'date_range_search_dom',
        ),
        'parent_type' => array(
            'name' => 'parent_type',
            'vname' => 'LBL_PARENT_TYPE',
            'type' => 'parent_type',
            'dbType' => 'varchar',
            'required' => false,
            'group' => 'parent_name',
            'options' => 'servicecalls_parent_type_display',
            'len' => 255,
            'comment' => 'The Sugar object to which the call is related'
        ),
        'parent_name' => array(
            'name' => 'parent_name',
            'parent_type' => 'servicecalls_record_type_display',
            'type_name' => 'parent_type',
            'id_name' => 'parent_id',
            'vname' => 'LBL_RELATED_TO',
            'type' => 'parent',
            'group' => 'parent_name',
            'source' => 'non-db',
            'options' => 'servicecalls_parent_type_display',
        ),
        'parent_id' => array(
            'name' => 'parent_id',
            'vname' => 'LBL_LIST_RELATED_TO_ID',
            'type' => 'id',
            'group' => 'parent_name',
            'reportable' => false,
            'comment' => 'The ID of the parent Sugar object identified by parent_type'
        ),
        'account_name' => array(
            'name' => 'account_name',
            'rname' => 'name',
            'id_name' => 'account_id',
            'vname' => 'LBL_ACCOUNT',
            'type' => 'relate',
            'link' => 'accounts',
            'table' => 'accounts',
            'join_name' => 'accounts',
            'isnull' => 'true',
            'module' => 'Accounts',
            'source' => 'non-db'
        ),
        'account_id' => array(
            'name' => 'account_id',
            'type' => 'varchar',
            'len' => 36,
            'rname' => 'id',
            'vname' => 'LBL_ACCOUNT_ID',
            'audited' => true,
        ),
        'accounts' => array(
            'vname' => 'LBL_ACCOUNTS',
            'name' => 'accounts',
            'type' => 'link',
            'module' => 'Accounts',
            'relationship' => 'servicecalls_accounts',
            'source' => 'non-db'
        ),
        'contact_name' => array(
            'name' => 'contact_name',
            'rname' => 'name',
            'id_name' => 'contact_id',
            'vname' => 'LBL_CONTACT',
            'type' => 'relate',
            'link' => 'contacts',
            'table' => 'contacts',
            'join_name' => 'contacts',
            'isnull' => 'true',
            'module' => 'Contacts',
            'source' => 'non-db'
        ),
        'contact_id' => array(
            'name' => 'contact_id',
            'type' => 'varchar',
            'len' => 36,
            'rname' => 'id',
            'vname' => 'LBL_CONTACT_ID',
            'audited' => true,
        ),
        'contacts' => array(
            'vname' => 'LBL_CONTACTS',
            'name' => 'contacts',
            'type' => 'link',
            'module' => 'Contacts',
            'relationship' => 'servicecalls_contacts',
            'source' => 'non-db'
        ),
        'sysservicecategory_id1' => array(
            'name' => 'sysservicecategory_id1',
            'vname' => 'LBL_SYSSERVICECATEGORY_ID1',
            'type' => 'id',
            'required' => true
        ),
        'sysservicecategory_id2' => array(
            'name' => 'sysservicecategory_id2',
            'vname' => 'LBL_SYSSERVICECATEGORY_ID2',
            'type' => 'id',
        ),
        'sysservicecategory_id3' => array(
            'name' => 'sysservicecategory_id3',
            'vname' => 'LBL_SYSSERVICECATEGORY_ID3',
            'type' => 'id',
        ),
        'sysservicecategory_id4' => array(
            'name' => 'sysservicecategory_id4',
            'vname' => 'LBL_SYSSERVICECATEGORY_ID4',
            'type' => 'id',
        ),

        //servicequeue
        'servicequeue_id' => array(
            'name' => 'servicequeue_id',
            'vname' => 'LBL_SERVICEQUEUE_ID',
            'type' => 'id',
        ),
        'servicequeue_name' => array(
            'name' => 'servicequeue_name',
            'vname' => 'LBL_SERVICEQUEUE',
            'type' => 'relate',
            'source' => 'non-db',
            'len' => '255',
            'id_name' => 'servicequeue_id',
            'rname' => 'name',
            'module' => 'ServiceQueues',
            'link' => 'servicequeues',
            'join_name' => 'servicequeues',
            'required' => false,
        ),
        'servicequeues' => array(
            'vname' => 'LBL_SERVICEQUEUES',
            'name' => 'servicequeues',
            'type' => 'link',
            'module' => 'ServiceQueues',
            'relationship' => 'servicecalls_servicequeues',
            'source' => 'non-db'
        ),

        //=> links
        'servicetickets' => array(
            'vname' => 'LBL_SERVICETICKETS',
            'name' => 'servicetickets',
            'type' => 'link',
            'module' => 'ServiceTickets',
            'relationship' => 'servicetickets_servicecalls',
            'source' => 'non-db',
            'link_type' => 'one'
        ),
        'servicefeedbacks' => array(
            'vname' => 'LBL_SERVICEFEEDBACKS',
            'name' => 'servicefeedbacks',
            'type' => 'link',
            'module' => 'ServiceFeedbacks',
            'relationship' => 'servicefeedbacks_servicecalls',
            'link_type' => 'one',
            'source' => 'non-db'
        ),
    ),
    'relationships' => array(
        'servicecalls_contacts' => array(
            'lhs_module' => 'Contacts',
            'lhs_table' => 'contacts',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceCalls',
            'rhs_table' => 'servicecalls',
            'rhs_key' => 'contact_id',
            'relationship_type' => 'one-to-many'
        ),
        'servicecalls_accounts' => array(
            'lhs_module' => 'Accounts',
            'lhs_table' => 'accounts',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceCalls',
            'rhs_table' => 'servicecalls',
            'rhs_key' => 'account_id',
            'relationship_type' => 'one-to-many',
        ),
        'servicecalls_servicequeues' => array(
            'lhs_module' => 'ServiceQueues',
            'lhs_table' => 'servicequeues',
            'lhs_key' => 'id',
            'rhs_module' => 'ServiceCalls',
            'rhs_table' => 'servicecalls',
            'rhs_key' => 'servicequeue_id',
            'relationship_type' => 'one-to-many'
        ),
    ),
    'indices' => array(
        array('name' => 'idx_servicecall_parent', 'type' => 'index', 'fields' => array('parent_id', 'parent_type')),
        array('name' => 'idx_servicecall_parenttype', 'type' => 'index', 'fields' => array('parent_type', 'deleted')),
    )

);


VardefManager::createVardef('ServiceCalls', 'ServiceCall', array('default', 'assignable'));
