<?php


$dictionary['salesplanningscopesets_reviewers'] = array(

	'table' => 'salesplanningscopesets_reviewers',

    'fields' => array(
        array('name' => 'id', 'type' => 'id'),
        array('name' => 'salesplanningscopeset_id', 'type' => 'id'),
        array('name' => 'user_id', 'type' => 'id'),
        array('name' => 'date_modified', 'type' => 'datetime'),
        array('name' => 'deleted', 'type' => 'bool', 'required' => true, 'default' => false)
    ),

   'indices' => array(
        array('name' => 'pk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_account_contact', 'type' => 'alternate_key', 'fields' => array('salesplanningscopeset_id', 'user_id')),
        array('name' => 'idx_account_del_contact', 'type' => 'index', 'fields'=> array('salesplanningscopeset_id', 'deleted', 'user_id'))
    ),

   'relationships' => array(
   		'salesplanningscopesets_reviewers' => array(
   			'lhs_module' => 'SalesPlanningScopeSets',
   			'lhs_table' => 'salesplanningscopesets',
   			'lhs_key' => 'id',
			'rhs_module' => 'Users',
			'rhs_table' => 'users',
			'rhs_key' => 'id',
			'relationship_type' => 'many-to-many',
			'join_table' => 'salesplanningscopesets_reviewers',
			'join_key_lhs' => 'salesplanningscopeset_id',
			'join_key_rhs' => 'user_id')
    )
);

$dictionary['salesplanningscopesets_salesplanningterritories'] = array (

	'table' => 'salesplanningscopesets_salesplanningterritories',

    'fields' => array(
        array('name' => 'id', 'type' => 'id'),
        array('name' => 'salesplanningscopeset_id', 'type' => 'id'),
        array('name' => 'salesplanningterritory_id', 'type' => 'id'),
        array('name' => 'date_modified', 'type' => 'datetime'),
        array('name' => 'deleted', 'type' => 'bool', 'required' => true, 'default' => false)
    ),

   'indices' => array(
        array('name' => 'pk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_scope_koo', 'type' => 'alternate_key', 'fields' => array('salesplanningscopeset_id', 'salesplanningterritory_id')),
        array('name' => 'idx_scope_del_koo', 'type' => 'index', 'fields'=> array('salesplanningscopeset_id', 'salesplanningterritory_id', 'deleted'))
    ),

   'relationships' => array(
   		'salesplanningscopesets_salesplanningterritories' => array(
   			'lhs_module' => 'SalesPlanningScopeSets',
   			'lhs_table' => 'salesplanningscopesets',
   			'lhs_key' => 'id',
			'rhs_module' => 'SalesPlanningTerritories',
			'rhs_table' => 'salesplanningterritories',
			'rhs_key' => 'id',
			'relationship_type' => 'many-to-many',
			'join_table' => 'salesplanningscopesets_salesplanningterritories',
			'join_key_lhs' => 'salesplanningscopeset_id',
			'join_key_rhs' => 'salesplanningterritory_id')
    )
);



$dictionary['salesplanningscopesets_salesplanningcharacteristics'] = array (

	'table' => 'salesplanningscopesets_salesplanningcharacteristics',

    'fields' => array(
        array('name' => 'id', 'type' => 'id'),
        array('name' => 'salesplanningscopeset_id', 'type' => 'id'),
        array('name' => 'salesplanningcharacteristic_id', 'type' => 'id'),
        array('name' => 'characteristic_sequence', 'type' => 'varchar', 'len' => 10),
        array('name' => 'date_modified', 'type' => 'datetime'),
        array('name' => 'deleted', 'type' => 'bool', 'required' => true, 'default' => false)
    ),

   'indices' => array(
        array('name' => 'pk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_scope_char', 'type' => 'alternate_key', 'fields' => array('salesplanningscopeset_id', 'salesplanningcharacteristic_id')),
        array('name' => 'idx_scope_del_char', 'type' => 'index', 'fields'=> array('salesplanningscopeset_id', 'salesplanningcharacteristic_id', 'deleted'))
    ),

   'relationships' => array(
   		'salesplanningscopesets_salesplanningcharacteristics' => array(
   			'lhs_module' => 'SalesPlanningScopeSets',
   			'lhs_table' => 'salesplanningscopesets',
   			'lhs_key' => 'id',
			'rhs_module' => 'SalesPlanningCharacteristics',
			'rhs_table' => 'salesplanningcharacteristics',
			'rhs_key' => 'id',
			'relationship_type' => 'many-to-many',
			'join_table' => 'salesplanningscopesets_salesplanningcharacteristics',
			'join_key_lhs' => 'salesplanningscopeset_id',
			'join_key_rhs' => 'salesplanningcharacteristic_id')
    )
);

//======= BEGIN maretval Orgunit to be checked !!! ===========// maretvalorgunitchecked: table is empty anyway... seems not to be in use
// $dictionary['salesplanningterritories_korgobjects'] = array (

// 	'table' => 'salesplanningterritories_korgobjects',

//     'fields' => array(
//         array('name' => 'id', 'type' => 'id'),
//         array('name' => 'salesplanningterritory_id', 'type' => 'id'),
//         array('name' => 'korgobject_id', 'type' => 'id'),
//         array('name' => 'date_modified', 'type' => 'datetime'),
//         array('name' => 'deleted', 'type' => 'bool', 'required' => true, 'default' => false)
//     ),

//    'indices' => array(
//         array('name' => 'pk', 'type' => 'primary', 'fields' => array('id')),
//         array('name' => 'idx_territory_koo', 'type' => 'alternate_key', 'fields' => array('salesplanningterritory_id', 'korgobject_id')),
//         array('name' => 'idx_territory_del_koo', 'type' => 'index', 'fields'=> array('salesplanningterritory_id', 'deleted', 'korgobject_id'))
//     ),

//    'relationships' => array(
//    		'salesplanningterritories_korgobjects' => array(
//    			'lhs_module' => 'SalesPlanningTerritories',
//    			'lhs_table' => 'salesplanningterritories',
//    			'lhs_key' => 'id',
// 			'rhs_module' => 'KOrgObjects',
// 			'rhs_table' => 'korgobjects',
// 			'rhs_key' => 'id',
// 			'relationship_type' => 'many-to-many',
// 			'join_table' => 'salesplanningterritories_korgobjects',
// 			'join_key_lhs' => 'salesplanningterritory_id',
// 			'join_key_rhs' => 'korgobject_id')
//     )
// );
//======= END maretval Orgunit to be checked !!! ===========//


$dictionary['salesplanningnodes_salesplanningcharacteristicvalues'] = array (

	'table' => 'salesplanningnodes_salesplanningcharacteristicvalues',

    'fields' => array(
        array('name' => 'id', 'type' => 'id'),
        array('name' => 'salesplanningnode_id', 'type' => 'id'),
        array('name' => 'salesplanningcharacteristicvalue_id', 'type' => 'id'),
        array('name' => 'date_modified', 'type' => 'datetime'),
        array('name' => 'deleted', 'type' => 'bool', 'required' => true, 'default' => false)
    ),

   'indices' => array(
        array('name' => 'pk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_node_charval', 'type' => 'alternate_key', 'fields' => array('salesplanningnode_id', 'salesplanningcharacteristicvalue_id')),
        array('name' => 'idx_node_del_charval', 'type' => 'index', 'fields'=> array('salesplanningnode_id', 'deleted', 'salesplanningcharacteristicvalue_id'))
    ),

   'relationships' => array(
   		'salesplanningnodes_salesplanningcharacteristicvalues' => array(
   			'lhs_module' => 'SalesPlanningNodes',
   			'lhs_table' => 'salesplanningnodes',
   			'lhs_key' => 'id',
			'rhs_module' => 'SalesPlanningCharacteristicValues',
			'rhs_table' => 'salesplanningcharacteristicvalues',
			'rhs_key' => 'id',
			'relationship_type' => 'many-to-many',
			'join_table' => 'salesplanningnodes_salesplanningcharacteristicvalues',
			'join_key_lhs' => 'salesplanningnode_id',
			'join_key_rhs' => 'salesplanningcharacteristicvalue_id')
    )
);


$dictionary['salesplanningcontents_salesplanningcontentfields'] = array (

	'table' => 'salesplanningcontents_salesplanningcontentfields',

    'fields' => array(
        array('name' => 'id', 'type' => 'id'),
        array('name' => 'salesplanningcontent_id', 'type' => 'id'),
        array('name' => 'salesplanningcontentfield_id', 'type' => 'id'),
        array('name' => 'sequence', 'type' => 'int', 'required' => true),
        array('name' => 'date_modified', 'type' => 'datetime'),
        array('name' => 'deleted', 'type' => 'bool', 'required' => true, 'default' => false)
    ),

   'indices' => array(
        array('name' => 'pk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_cont_del', 'type' => 'index', 'fields'=> array('salesplanningcontent_id', 'deleted'))
    ),

   'relationships' => array(
   		'salesplanningcontents_salesplanningcontentfields' => array(
   			'lhs_module' => 'SalesPlanningContents',
   			'lhs_table' => 'salesplanningcontents',
   			'lhs_key' => 'id',
			'rhs_module' => 'SalesPlanningContentFields',
			'rhs_table' => 'salesplanningcontentfields',
			'rhs_key' => 'id',
			'relationship_type' => 'many-to-many',
			'join_table' => 'salesplanningcontents_salesplanningcontentfields',
			'join_key_lhs' => 'salesplanningcontent_id',
			'join_key_rhs' => 'salesplanningcontentfield_id')
    )
);

$dictionary['salesplanningversions_salesplanningcontents'] = array (

	'table' => 'salesplanningversions_salesplanningcontents',

    'fields' => array(
        array('name' => 'id', 'type' => 'id'),
        array('name' => 'salesplanningversion_id', 'type' => 'id'),
        array('name' => 'salesplanningcontent_id', 'type' => 'id'),
        array('name' => 'date_modified', 'type' => 'datetime'),
        array('name' => 'deleted', 'type' => 'bool', 'required' => true, 'default' => false)
    ),

   'indices' => array(
        array('name' => 'pk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_version_del', 'type' => 'index', 'fields'=> array('salesplanningversion_id', 'deleted'))
    ),

   'relationships' => array(
   		'salesplanningversions_salesplanningcontents' => array(
   			'lhs_module' => 'SalesPlanningVersions',
   			'lhs_table' => 'salesplanningversions',
   			'lhs_key' => 'id',
			'rhs_module' => 'SalesPlanningContents',
			'rhs_table' => 'salesplanningcontents',
			'rhs_key' => 'id',
			'relationship_type' => 'many-to-many',
			'join_table' => 'salesplanningversions_salesplanningcontents',
			'join_key_lhs' => 'salesplanningversion_id',
			'join_key_rhs' => 'salesplanningcontent_id')
    )
);


$dictionary['salesplanningnodes_masterdata'] = array (

	'table' => 'salesplanningnodes_masterdata',

    'fields' => array(
        array('name' => 'id', 'type' => 'id'),
        array('name' => 'salesplanningversion_id', 'type' => 'id'),
        array('name' => 'salesplanningnode_id', 'type' => 'id'),
        array('name' => 'created_by', 'type' => 'varchar', 'len' => 36),
        array('name' => 'date_entered', 'type' => 'datetime'),
        array('name' => 'modified_by', 'type' => 'varchar', 'len' => 36),
        array('name' => 'date_modified', 'type' => 'datetime'),
        array('name' => 'deleted', 'type' => 'bool', 'required' => true, 'default' => false),
        array('name' => 'marked_as_done', 'type' => 'bool', 'default' => false),
        array('name' => 'notice', 'type' => 'text'),
    ),

   'indices' => array(
        array('name' => 'pk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_version_node_del', 'type' => 'index', 'fields'=> array('salesplanningversion_id', 'salesplanningnode_id', 'deleted'))
    ),

   'relationships' => array(
    )
);
