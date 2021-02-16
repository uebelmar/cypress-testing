<?php

/*
$dictionary ['KSalesTaxDetermination'] = array(
    'table' => 'ksalestaxdetermination',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'id',
            'required' => true,
            'reportable' => false),
        'fromcountry' => array(
            'name' => 'fromcountry',
            'vname' => 'LBL_FROMCOUNTRY',
            'type' => 'enum',
            'options' => 'ISO3116',
            'len' => 2),
        'tocountry' => array(
            'name' => 'tocountry',
            'vname' => 'LBL_TOCOUNTRY',
            'type' => 'enum',
            'options' => 'ISO3116',
            'len' => 2),
        'taxcategory_product' => array(
            'name' => 'taxcategory_product',
            'vname' => 'LBL_TAXCATEGORY_PRODUCT',
            'type' => 'enum',
            'len' => 1,
            'options' => 'ktaxindicators'),
        'taxcategory_customer' => array(
            'name' => 'taxcategory_customer',
            'vname' => 'LBL_TAXCATEGORY_CUSTOMER',
            'type' => 'enum',
            'len' => 1,
            'options' => 'ktaxindicatorsc'),
        'tax_category' => array(
            'name' => 'tax_category',
            'vname' => 'LBL_TAX_CATEGORY',
            'type' => 'enum',
            'options' => 'ktaxcategories',
            'kconfigurator' => array(
                'foreignkey' => 'kaccountingtaxcategories.taxcategoryid'
            ),
            'len' => 5
        )
    ),
    'indices' => array(
        array(
            'name' => 'id',
            'type' => 'primary',
            'fields' => array('id')
        )
    )
);
*/
$dictionary ['sysSalesDocTaxCategories'] = array(
    'table' => 'syssalesdoctaxcategories',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'id'
        ),
        'taxcategoryid' => array(
            'name' => 'taxcategoryid',
            'vname' => 'LBL_TAXCATEGORYID',
            'type' => 'varchar',
            'len' => 5,
            'required' => true,
            'reportable' => false),
        'taxcategoryname' => array(
            'name' => 'taxcategoryname',
            'vname' => 'LBL_TAXCATEGORYNAME',
            'type' => 'varchar',
            'len' => 60),
        'taxpercentage' => array(
            'name' => 'taxpercentage',
            'vname' => 'LBL_TAXPERCENTAGE',
            'type' => 'double'),
        'taxscope' => array(
            'name' => 'taxscope',
            'vname' => 'LBL_TAXSCOPE',
            'type' => 'varchar',
            'len' => 1)
    ),
    'indices' => array(
        array(
            'name' => 'syssalesdoctaxcategories_id',
            'type' => 'primary',
            'fields' => array('id')
        )
    )
);

$dictionary ['sysSalesDocTypes'] = array(
    'table' => 'syssalesdoctypes',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'id'
        ),
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'varchar',
            'len' => 25,
            'required' => true
        ),
        'vname' => array(
            'name' => 'vname',
            'vname' => 'LBL_vname',
            'type' => 'varchar',
            'len' => 50,
            'required' => false
        ),
        'salesdoccategory' => array(
            'name' => 'salesdoccategory',
            'vname' => 'LBL_SALESDOCCATEGORY',
            'type' => 'varchar',
            'len' => 2
        ),
        'salesdocparty' => array(
            'name' => 'salesdocparty',
            'vname' => 'LBL_SALESDOCPARTY',
            'type' => 'enum',
            'len' => 1,
            'options' => 'salesdoc_docparties',
            'required' => true
        ),
        'headercomponentset' => array(
            'name' => 'headercomponentset',
            'vname' => 'LBL_HEADERCOMPONENTSET',
            'type' => 'varchar',
            'len' => 36
        ),
        'footercomponentset' => array(
            'name' => 'footercomponentset',
            'vname' => 'LBL_FOOTERCOMPONENTSET',
            'type' => 'varchar',
            'len' => 36
        ),
        'displayonly' => array(
            'name' => 'displayonly',
            'vname' => 'LBL_DISPLAYONLY',
            'type' => 'bool'
        ),
        'description' => array(
            'name' => 'description',
            'vname' => 'LBL_DESCRIPTION',
            'type' => 'text'
        ),
        'aftersavehook' => array(
            'name' => 'aftersavehook',
            'vname' => 'LBL_SAVEHOOK',
            'type' => 'varchar',
            'len' => '200',
            'comment' => 'a classnaame and method that will be executed after the document has been saved'
        )
    ),
    'indices' => array(
        array(
            'name' => 'syssalesdoctypes_id',
            'type' => 'primary',
            'fields' => array('id')
        )
    )
);


$dictionary ['sysSalesDocItemTypes'] = array(
    'table' => 'syssalesdocitemtypes',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'id'
        ),
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'varchar',
            'len' => 25,
            'required' => true
        ),
        'vname' => array(
            'name' => 'vname',
            'vname' => 'LBL_vname',
            'type' => 'varchar',
            'len' => 50,
            'required' => false
        ),
        'addmodalcomponent' => array(
            'name' => 'addmodalcomponent',
            'vname' => 'LBL_ADDMODALCOMPONENT',
            'type' => 'varchar',
            'len' => 100
        ),
        'addmodalfilter' => array(
            'name' => 'addmodalfilter',
            'vname' => 'LBL_ADDMODALFILTER',
            'type' => 'varchar',
            'len' => 36,
            'comment' => 'an optional filter id that will be passed to the modal component'
        ),
        'detailcomponentset' => array(
            'name' => 'detailcomponentset',
            'vname' => 'LBL_DETAILCOMPONENTSET',
            'type' => 'varchar',
            'len' => 36
        ),
        'itemfieldset' => array(
            'name' => 'itemfieldset',
            'vname' => 'LBL_ITEMFIELDSET',
            'type' => 'varchar',
            'len' => 36
        ),
        'pricerelevant' => array(
            'name' => 'pricerelevant',
            'vname' => 'LBL_PRICERELEVANT',
            'type' => 'bool'
        ),
        'aftersavehook' => array(
            'name' => 'aftersavehook',
            'vname' => 'LBL_SAVEHOOK',
            'type' => 'varchar',
            'len' => '200',
            'comment' => 'a classnaame and method that will be executed after the item has been saved'
        )
    ),
    'indices' => array(
        array(
            'name' => 'syssalesdocitemtypes_id',
            'type' => 'primary',
            'fields' => array('id')
        )
    )
);

$dictionary ['sysSalesDocTypesItemTypes'] = array(
    'table' => 'syssalesdoctypesitemtypes',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'id'
        ),
        'salesdoctype' => array(
            'name' => 'salesdoctype',
            'vname' => 'LBL_SALESDOCTYPE',
            'type' => 'varchar',
            'len' => 36
        ),
        'salesdocitemtype' => array(
            'name' => 'salesdocitemtype',
            'vname' => 'LBL_SALESDOCITEMTYPE',
            'type' => 'varchar',
            'len' => 36
        )
    ),
    'indices' => array(
        array(
            'name' => 'syssalesdoctypesitemtypes_id',
            'type' => 'primary',
            'fields' => array('id')
        )
    )
);

$dictionary ['sysSalesDocNumberRanges'] = array(
    'table' => 'syssalesdocnumberranges',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'id'
        ),
        'syssalesdoctype' => array(
            'name' => 'syssalesdoctype',
            'vname' => 'LBL_SYSSALESDOCTYPE',
            'type' => 'varchar',
            'len' => 25,
            'required' => true
        ),
        'companycode_id' => array(
            'name' => 'companycode_id',
            'vname' => 'LBL_COMPANYCODE_ID',
            'type' => 'varchar',
            'len' => 50,
            'required' => false
        ),
        'numberrange' => array(
            'name' => 'numberrange',
            'type' => 'varchar',
            'len' => 36,
        ),
        'valid_from' => array(
            'name' => 'valid_from',
            'type' => 'date'
        ),
        'valid_to' => array(
            'name' => 'valid_to',
            'type' => 'date'
        )
    ),
    'indices' => array(
        array(
            'name' => 'syssalesdocnumberranges_id',
            'type' => 'primary',
            'fields' => array('id')
        )
    )
);

$dictionary ['sysSalesDocTypesFlow'] = array(
    'table' => 'syssalesdoctypesflow',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'id'
        ),
        'salesdoctype_from' => array(
            'name' => 'salesdoctype_from',
            'vname' => 'LBL_SALESDOCTYPE_FROM',
            'type' => 'varchar',
            'len' => 36
        ),
        'salesdoctype_to' => array(
            'name' => 'salesdoctype_to',
            'vname' => 'LBL_SALESDOCTYPE_TO',
            'type' => 'varchar',
            'len' => 36
        ),
        'convert_method' => array(
            'name' => 'convert_method',
            'vname' => 'LBL_CONVERT_METHOD',
            'type' => 'varchar',
            'len' => 100,
            'comment' => 'a class and method to identify the processing of the document to be applied when copiyng from one document to the next'
        ),
        'track' => array(
            'name' => 'track',
            'vname' => 'LBL_TRACK',
            'type' => 'bool',
            'comment' => 'if set to true the link will be tracked linking the two documents int he document flow. Can be omitted e.g. when coping quotes to quotes and others'
        )
    ),
    'indices' => array(
        array(
            'name' => 'syssalesdoctypesflow_id',
            'type' => 'primary',
            'fields' => array('id')
        )
    )
);

$dictionary ['sysSalesDocItemTypesFlow'] = array(
    'table' => 'syssalesdocitemtypesflow',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'id'
        ),
        'salesdoctype_from' => array(
            'name' => 'salesdoctype_from',
            'vname' => 'LBL_SALESDOCTYPE_FROM',
            'type' => 'varchar',
            'len' => 36
        ),
        'salesdocitemtype_from' => array(
            'name' => 'salesdocitemtype_from',
            'vname' => 'LBL_SALESDOCITEMTYPE_FROM',
            'type' => 'varchar',
            'len' => 36
        ),
        'salesdoctype_to' => array(
            'name' => 'salesdoctype_to',
            'vname' => 'LBL_SALESDOCTYPE_TO',
            'type' => 'varchar',
            'len' => 36
        ),
        'salesdocitemtype_to' => array(
            'name' => 'salesdocitemtype_to',
            'vname' => 'LBL_SALESDOCITEMTYPE_TO',
            'type' => 'varchar',
            'len' => 36
        ),
        'convert_method' => array(
            'name' => 'convert_method',
            'vname' => 'LBL_CONVERT_METHOD',
            'type' => 'varchar',
            'len' => 100,
            'comment' => 'a class and method to identify the processing of the item to be applied when copying from one item to the next'
        ),
        'quantityhandling' => array(
            'name' => 'quantityhandling',
            'vname' => 'LBL_QUANTITYHANDLING',
            'type' => 'char',
            'len' => 1,
            'default' => '0',
            'comment' => 'defines the quantitiy handling, 0 has no impact, - reduces the open quantity, + adds to the open quantity'
        )
    ),
    'indices' => array(
        array(
            'name' => 'syssalesdocitemtypesflow_id',
            'type' => 'primary',
            'fields' => array('id')
        )
    )
);
