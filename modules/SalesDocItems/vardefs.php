<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['SalesDocItem'] = array(
    'table' => 'salesdocitems',
    'audited' => true,
    'fields' => array(
        'salesdoc_id' => array(
            'name' => 'salesdoc_id',
            'vname' => 'LBL_SALESDOC_ID',
            'type' => 'id',
            'audited' => true,
        ),
        'salesdoc' => array(
            'name' => 'salesdoc',
            'type' => 'link',
            'vname' => 'LBL_SALESDOC',
            'relationship' => 'salesdocs_salesdocitems',
            'source' => 'non-db',
            'module' => 'SalesDocs'
        ),
        'product_id' => array(
            'name' => 'product_id',
            'vname' => 'LBL_PRODUCT_ID',
            'type' => 'varchar',
            'len' => 36,
            'audited' => true
        ),
        'products' => array(
            'name' => 'products',
            'vname' => 'LBL_PRODUCTS',
            'type' => 'link',
            'relationship' => 'products_salesdocitems',
            'source' => 'non-db'
        ),
        'product_name' => array(
            'name' => 'product_name',
            'rname' => 'name',
            'id_name' => 'product_id',
            'vname' => 'LBL_PRODUCT',
            'join_name' => 'products',
            'type' => 'relate',
            'link' => 'products',
            'table' => 'products',
            'isnull' => 'true',
            'module' => 'Products',
            'dbType' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
        ),
        'productgroup_id' => array(
            'name' => 'productgroup_id',
            'vname' => 'LBL_PRODUCTGROUP_ID',
            'type' => 'varchar',
            'len' => 36,
            'audited' => true
        ),
        'productgroups' => array(
            'name' => 'productgroups',
            'vname' => 'LBL_PRODUCGROUPTS',
            'type' => 'link',
            'relationship' => 'productgroups_salesdocitems',
            'source' => 'non-db'
        ),
        'productgroup_name' => array(
            'name' => 'productgroup_name',
            'rname' => 'name',
            'id_name' => 'productgroup_id',
            'vname' => 'LBL_PRODUCTGROUP',
            'join_name' => 'productgroups',
            'type' => 'relate',
            'link' => 'productgroups',
            'table' => 'productgroups',
            'isnull' => 'true',
            'module' => 'ProductGroups',
            'dbType' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
        ),
        'productvariant_id' => array(
            'name' => 'productvariant_id',
            'vname' => 'LBL_PRODUCTVARIANT_ID',
            'type' => 'char',
            'len' => 36,
            'dbType' => 'id',
            'audited' => true
        ),
        'productvariants' => array(
            'name' => 'productvariants',
            'vname' => 'LBL_PRODUCTVARIANTS',
            'type' => 'link',
            'relationship' => 'productvariants_salesdocitems',
            'source' => 'non-db'
        ),
        'productvariant_name' => array(
            'name' => 'productvariant_name',
            'rname' => 'name',
            'id_name' => 'productvariant_id',
            'vname' => 'LBL_PRODUCTVARIANT',
            'join_name' => 'productvariantss',
            'type' => 'relate',
            'link' => 'productvariants',
            'table' => 'productvariants',
            'isnull' => 'true',
            'module' => 'ProductVariants',
            'dbType' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
        ),
        'itemnr' => array(
            'name' => 'itemnr',
            'vname' => 'LBL_ITEM_NR',
            'type' => 'varchar',
            'len' => 6,
            'audited' => true
        ),
        'parentitem_id' => array(
            'name' => 'parentitem_id',
            'vname' => 'LBL_PARENTITEM_ID',
            'type' => 'varchar',
            'len' => 36,
            'audited' => true
        ),
        'itemtype' => array(
            'name' => 'itemtype',
            'vname' => 'LBL_ITEM_TYPE',
            'type' => 'salesdocitemtype',
            'dbtype' => 'varchar',
            'len' => 5,
            'audited' => true
        ),
        'tax_category' => array(
            'name' => 'tax_category',
            'vname' => 'LBL_TAX_CATEGORY',
            'type' => 'taxcategories',
            'dbtype' => 'varchar',
            'len' => 5,
            'required' => false,
            'reportable' => true,
            'massupdate' => false,
        ),
        'quantity' => array(
            'name' => 'quantity',
            'vname' => 'LBL_QUANTITY',
            'type' => 'quantity',
            'dbtype' => 'double',
            'required' => false
        ),
        'delivery_date' => array(
            'name' => 'delivery_date',
            'vname' => 'LBL_DELIVERY_DATE',
            'type' => 'date',
            'required' => false
        ),
        'uom_id' => array(
            'name' => 'uom_id',
            'vname' => 'LBL_UOM',
            'type' => 'unitofmeasure',
            'dbtype' => 'varchar',
            'len' => 36
        ),
        'amount_net_per_uom' => array(
            'name' => 'amount_net_per_uom',
            'vname' => 'LBL_AMOUNT_PER_UNIT',
            'type' => 'currency',
            'dbType' => 'double',
            'currency_id' => 'currency_id',
            'required' => false
        ),
        'amount_net' => array(
            'name' => 'amount_net',
            'vname' => 'LBL_AMOUNT_NET',
            'type' => 'currency',
            'dbType' => 'double',
            'currency_id' => 'currency_id',
            'required' => false,
            'audited' => true

        ),
        'amount_gross' => array(
            'name' => 'amount_gross',
            'vname' => 'LBL_AMOUNT_GROSS',
            'type' => 'currency',
            'dbType' => 'double',
            'currency_id' => 'currency_id',
            'required' => false,
            'audited' => true

        ),
        'purchase_price' => array(
            'name' => 'purchase_price',
            'vname' => 'LBL_PURCHASE_PRICE',
            'type' => 'currency',
            'dbType' => 'double'
        ),
        'tax_amount' => array(
            'name' => 'tax_amount',
            'vname' => 'LBL_TAX_AMOUNT',
            'type' => 'currency',
            'dbType' => 'double',
            'currency_id' => 'currency_id',
            'required' => false,
            'audited' => true
        ),
        'currency_id' => array(
            'name' => 'currency_id',
            'type' => 'id',
            'group' => 'currency_id',
            'vname' => 'LBL_CURRENCY',
            'reportable' => false,
        ),
        'rejection_reason' => array(
            'name' => 'rejection_reason',
            'type' => 'enum',
            'len' => 25,
            'vname' => 'LBL_REJECTION_REASON',
            'options' => 'salesdocitem_rejection_reasons_dom'
        ),
        'rejection_text' => array(
            'name' => 'rejection_text',
            'type' => 'text',
            'vname' => 'LBL_REJECTION_TEXT'
        ),

        'salesdocitemsquotes2orders' => array(
            'name' => 'salesdocitemsquotes2orders',
            'type' => 'link',
            'vname' => 'LBL_SALESDOCITEMSQUOTES2ORDERS',
            'relationship' => 'salesdocitemsquotes_salesdocitemsorders',
            'source' => 'non-db',
        ),
        'salesdocitemsorders2quotes' => array(
            'name' => 'salesdocitemsorders2quotes',
            'type' => 'link',
            'vname' => 'LBL_SALESDOCITEMSORDERS2QUOTES',
            'relationship' => 'salesdocitemsquotes_salesdocitemsorders',
            'source' => 'non-db',
        ),

        'salesdocitemsquotes2contracts' => array(
            'name' => 'salesdocitemsquotes2contracts',
            'type' => 'link',
            'vname' => 'LBL_SALESDOCITEMSQUOTES2CONTRACTS',
            'relationship' => 'salesdocitemsquotes_salesdocitemscontracts',
            'source' => 'non-db',
        ),
        'salesdocitemscontracts2quotes' => array(
            'name' => 'salesdocitemscontracts2quotes',
            'type' => 'link',
            'vname' => 'LBL_SALESDOCITEMSCONTRACTS2QUOTES',
            'relationship' => 'salesdocitemsquotes_salesdocitemscontracts',
            'source' => 'non-db',
        ),

        'salesdocitemsorders2invoices' => array(
            'name' => 'salesdocitemsorders2invoices',
            'type' => 'link',
            'vname' => 'LBL_SALESDOCSORDERS2INVOICES',
            'relationship' => 'salesdocitemsorders_salesdocitemsinvoices',
            'source' => 'non-db',
        ),
        'salesdocitemsinvoices2orders' => array(
            'name' => 'salesdocitemsinvoices2orders',
            'type' => 'link',
            'vname' => 'LBL_SALESDOCSINVOICES2ORDERS',
            'relationship' => 'salesdocitemsorders_salesdocitemsinvoices',
            'source' => 'non-db',
        ),

        'salesdocitemscontracts2invoices' => array(
            'name' => 'salesdocitemscontracts2invoices',
            'type' => 'link',
            'vname' => 'LBL_SALESDOCITEMSCONTRACTS2INVOICES',
            'relationship' => 'salesdocitemscontracts_salesdocitemsinvoices',
            'source' => 'non-db',
        ),
        'salesdocitemsinvoices2contracts' => array(
            'name' => 'salesdocitemsinvoices2contracts',
            'type' => 'link',
            'vname' => 'LBL_SALESDOCITEMSINVOICES2CONTRACTS',
            'relationship' => 'salesdocitemscontracts_salesdocitemsinvoices',
            'source' => 'non-db',
        ),
        'salesvouchers' => [
            'name' => 'salesvouchers',
            'type' => 'link',
            'relationship' => 'salesdocitems_salesvouchers',
            'source' => 'non-db',
            'module' => 'SalesVouchers'
        ],
        'salesdocitemconditions' => [
            'name' => 'salesdocitemconditions',
            'type' => 'link',
            'vname' => 'LBL_SALESDOCITEMCONDITIONS',
            'relationship' => 'salesdocitem_salesdocitemconditionss',
            'source' => 'non-db',
            'module' => 'SalesDocItemConditions'
        ],
        'originating_id' => [
            'name' => 'originating_id',
            'source' => 'non-db'
        ],
        'parent_documentitems' => [
            'name' => 'parent_documentitems',
            'vname' => 'LBL_PARENT_DOCUMENTITEMS',
            'type' => 'link',
            'source' => 'non-db',
            'module' => 'SalesDocItems',
            'relationship' => 'salesdocsitemsflow',
            'side' => 'right'
        ],
        'child_documentitems' => [
            'name' => 'child_documentitems',
            'vname' => 'LBL_CHILD_DOCUMENTITEMS',
            'type' => 'link',
            'source' => 'non-db',
            'module' => 'SalesDocItems',
            'relationship' => 'salesdocsitemsflow',
            'side' => 'left'
        ]
    ),
    'indices' => array(
        array('name' => 'idx_salesdocitems_id_del', 'type' => 'index', 'fields' => array('id', 'deleted'),),
        array('name' => 'idx_salesdocitems_salesdoc_id_del', 'type' => 'index', 'fields' => array('salesdoc_id', 'deleted'),),
    ),
    'relationships' => array(
        'salesdocs_salesdocitems' => array(
            'lhs_module' => 'SalesDocs',
            'lhs_table' => 'salesdocs',
            'lhs_key' => 'id',
            'rhs_module' => 'SalesDocItems',
            'rhs_table' => 'salesdocitems',
            'rhs_key' => 'salesdoc_id',
            'relationship_type' => 'one-to-many'
        ),
        'products_salesdocitems' => array(
            'rhs_module' => 'Products',
            'rhs_table' => 'products',
            'rhs_key' => 'id',
            'lhs_module' => 'SalesDocItems',
            'lhs_table' => 'salesdocitems',
            'lhs_key' => 'product_id',
            'relationship_type' => 'one-to-many'
        ),
        'productgroups_salesdocitems' => array(
            'rhs_module' => 'ProductGroups',
            'rhs_table' => 'productgroups',
            'rhs_key' => 'id',
            'lhs_module' => 'SalesDocItems',
            'lhs_table' => 'salesdocitems',
            'lhs_key' => 'productgroup_id',
            'relationship_type' => 'one-to-many'
        ),
        'productvariants_salesdocitems' => array(
            'rhs_module' => 'ProductVariants',
            'rhs_table' => 'productvariants',
            'rhs_key' => 'id',
            'lhs_module' => 'SalesDocItems',
            'lhs_table' => 'salesdocitems',
            'lhs_key' => 'productvariant_id',
            'relationship_type' => 'one-to-many'
        )
    ),
    'optimistic_lock' => true
);



VardefManager::createVardef('SalesDocItems', 'SalesDocItem', array('default', 'assignable'));

