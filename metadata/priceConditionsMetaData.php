<?php


$dictionary ['syspriceconditiontypes'] = array(
    'table' => 'syspriceconditiontypes',
    'comment' => 'holds the PriceCondition types used for the flexioble price determination in Salesedocs',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'id',
            'comment' => 'the id of the record'
        ),
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'varchar',
            'len' => 50,
            'required' => true,
            'comment' => 'a unique identifier, name for the '
        ),
        'ext_id' => array(
            'name' => 'ext_id',
            'vname' => 'LBL_EXT_ID',
            'type' => 'varchar',
            'len' => 25,
            'required' => false,
            'comment' => 'an extenral id, in SAP Integration used to hold the Condition key KSCHL'
        ),
        'label' => array(
            'name' => 'label',
            'vname' => 'LBL_LABEL',
            'type' => 'varchar',
            'len' => 50,
            'required' => false,
            'comment' => 'the label to display the condition name'
        ),
        'valuetype' => array(
            'name' => 'valuetype',
            'vname' => 'LBL_VALUETYPE',
            'type' => 'varchar',
            'len' => 1,
            'required' => true,
            'comment' => 'values A or P, defines if the type is an absolute (A) a mount or a percentage (P)'
        ),
        'sortindex' => array(
            'name' => 'sortindex',
            'vname' => 'LBL_SORTINDEX',
            'type' => 'int',
            'required' => false,
            'comment' => 'a sort value to sequence the records in a list'
        )
    ),
    'indices' => array(
        array(
            'name' => 'syspriceconditiontypes_id',
            'type' => 'primary',
            'fields' => array('id')
        )
    )
);

$dictionary ['syspricedeterminations'] = array(
    'table' => 'syspricedeterminations',
    'comment' => 'defines strategies to access reords for determination of a price',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'id',
            'comment' => 'the id of the record'
        ),
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'varchar',
            'len' => 50,
            'required' => true,
            'comment' => 'a unique identifier, name for the '
        ),
        'ext_id' => array(
            'name' => 'ext_id',
            'vname' => 'LBL_EXT_ID',
            'type' => 'varchar',
            'len' => 25,
            'required' => false,
            'comment' => 'an extenral id, in SAP Integration used to hold the access table key KOTABNR'
        ),
        'label' => array(
            'name' => 'label',
            'vname' => 'LBL_LABEL',
            'type' => 'varchar',
            'len' => 50,
            'required' => false,
            'comment' => 'the label to display the condition name'
        ),
        'sortindex' => array(
            'name' => 'sortindex',
            'vname' => 'LBL_SORTINDEX',
            'type' => 'int',
            'required' => false,
            'comment' => 'a sort value to sequence the records in a list'
        )
    ),
    'indices' => array(
        array(
            'name' => 'syspriceconditiontypedet_id',
            'type' => 'primary',
            'fields' => array('id')
        )
    )
);

$dictionary ['syspriceconditiontypes_determinations'] = array(
    'table' => 'syspriceconditiontypes_determinations',
    'comment' => 'holds the PriceCondition types used for the flexioble price determination in Salesedocs',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'id',
            'comment' => 'the id of the record'
        ),
        'priceconditiontype_id' => array(
            'name' => 'priceconditiontype_id',
            'vname' => 'LBL_PRICECONDITIONTYPE',
            'type' => 'id',
            'required' => true,
            'comment' => 'the id of the pricing condition'
        ),
        'pricedetermination_id' => array(
            'name' => 'pricedetermination_id',
            'vname' => 'LBL_PRICEDETERMINATION',
            'type' => 'id',
            'required' => true,
            'comment' => 'the id of the pricing determination'
        ),
        'pricedetermination_index' => array(
            'name' => 'pricedetermination_index',
            'vname' => 'LBL_INDEX',
            'type' => 'int',
            'required' => true,
            'comment' => 'the index of the determination'
        )
    ),
    'indices' => array(
        array(
            'name' => 'syspriceconditiontypes_id',
            'type' => 'primary',
            'fields' => array('id')
        )
    )
);

$dictionary ['syspriceconditionelements'] = array(
    'table' => 'syspriceconditionelements',
    'comment' => 'defines the elements a priceconditiontypes can use to determine prices',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'id',
            'comment' => 'the id of the record'
        ),
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'varchar',
            'len' => 50,
            'required' => true,
            'comment' => 'the display name for the element'
        ),
        'ext_id' => array(
            'name' => 'ext_id',
            'vname' => 'LBL_EXT_ID',
            'type' => 'varchar',
            'len' => 25,
            'required' => false,
            'comment' => 'an extenral id, in SAP Integration used to hold the Condition key KSCHL'
        ),
        'element_length' => array(
            'name' => 'element_length',
            'vname' => 'LBL_ELEMENT_LENGTH',
            'type' => 'varchar',
            'len' => 3,
            'required' => true,
            'comment' => 'the length used to build the key'
        ),
        'element_domain' => array(
            'name' => 'element_domain',
            'vname' => 'LBL_ELEMENT_DOMAIN',
            'type' => 'varchar',
            'len' => 50,
            'comment' => 'a domain name if the values are tied to a domain defined in teh system'
        ),
        'element_module' => array(
            'name' => 'element_module',
            'vname' => 'LBL_ELEMENT_MODULE',
            'type' => 'varchar',
            'len' => 80,
            'comment' => 'the name of a module if this elem,ent is linked to a foreign key record in another module'
        ),
        'element_module_field' => array(
            'name' => 'element_module_field',
            'vname' => 'LBL_ELEMENT_MODULE_FIELD',
            'type' => 'varchar',
            'len' => 80,
            'comment' => 'the name of a field in the foreign module that is used to build the key .. e.g. the customer number'
        ),
        'label' => array(
            'name' => 'label',
            'vname' => 'LBL_LABEL',
            'type' => 'varchar',
            'len' => 50,
            'required' => false,
            'comment' => 'the label to display the condition name'
        )
    ),
    'indices' => array(
        array(
            'name' => 'syspriceconditionelements_id',
            'type' => 'primary',
            'fields' => array('id')
        )
    )
);

$dictionary ['syspricedeterminationelements'] = array(
    'table' => 'syspricedeterminationelements',
    'comment' => 'links the elements with the price determination startegy and also builds a sequence',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'id',
            'comment' => 'the id of the record'
        ),
        'pricedetermination_id' => array(
            'name' => 'pricedetermination_id',
            'vname' => 'LBL_PRICEDETERMINATION',
            'type' => 'id',
            'required' => true,
            'comment' => 'the id of the determination record'
        ),
        'priceconditionelement_id' => array(
            'name' => 'priceconditionelement_id',
            'vname' => 'LBL_PRICECONDITIONELEMENT',
            'type' => 'id',
            'required' => true,
            'comment' => 'the id of the element'
        ),
        'priceconditionelement_index' => array(
            'name' => 'priceconditionelement_index',
            'vname' => 'LBL_INDEX',
            'type' => 'int',
            'required' => true,
            'comment' => 'the index of the element in the allocation building the sequence'
        )
    ),
    'indices' => array(
        array(
            'name' => 'syspriceconditiondetelements_id',
            'type' => 'primary',
            'fields' => array('id')
        )
    )
);
