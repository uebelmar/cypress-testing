<?php
/**
 * future tables to retrieve labels translations from
 * User: maretval
 * Date: 22.12.2017
 * Time: 13:57
 */

/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/


$dictionary['syslangs'] = array (
    'table' => 'syslangs',
    'changerequests' => array(
        'active' => true,
        'name' => 'language_code'
    ),
    'fields' => array (
        'id' => array (
            'name' => 'id',
            'vname' => 'LBL_ID',
            'type' => 'id',
            'required' => true,
        ),
        'language_code' => array (
            'name' => 'language_code',
            'vname' => 'LBL_LANGUAGE',
            'type' => 'char',
            'len' => '10',
            'required' => true,
        ),
        'language_name' => array (
            'name' => 'language_name',
            'vname' => 'LBL_LANGUAGE',
            'type' => 'char',
            'len' => '50',
            'required' => true,
        ),
        'sort_sequence' => array (
            'name' => 'sort_sequence',
            'vname' => 'LBL_SORT_SEQUENCE',
            'type' => 'int',
            'default' => 99
        ),
        'is_default' => array (
            'name' => 'is_default',
            'vname' => 'LBL_IS_DEFAULT',
            'type' => 'bool',
        ),
        'system_language' => array (
            'name' => 'system_language',
            'vname' => 'LBL_SYSTEM_LANGUAGE',
            'type' => 'bool',
        ),
        'communication_language' => array (
            'name' => 'communication_language',
            'vname' => 'LBL_COMMUNICATION_LANGUAGE',
            'type' => 'bool',
        )
    ),
    'indices' => array (
        array('name' => 'syslanguagespk', 'type' =>'primary','fields' => array('id')),
        array('name' => 'syslanguages_idx', 'type' =>'index','fields' => array('language_code')),
        array('name' => 'syslanguagesdefault_idx', 'type' =>'index','fields' => array('is_default')),
        array('name' => 'syslanguageslangdefault_idx', 'type' =>'index','fields' => array('language_code', 'is_default')),
    ),
);

$dictionary['syslanguagelabels'] = array (
    'table' => 'syslanguagelabels',
    'fields' => array (
        'id' => array (
            'name' => 'id',
            'vname' => 'LBL_ID',
            'type' => 'id'
        ),
        'name' => array (
            'name' => 'name',
            'vname' => 'LBL_LABEL',
            'type' => 'varchar',
            'len' => '100',
            'required' => true,
        ),
// removed in spice 2020.03.001
//        'version' => array (
//            'name' => 'version',
//            'vname' => 'LBL_VERSION',
//            'type' => 'varchar',
//            'len' => 16,
//        ),
//        'package' => array(
//            'name' => 'package',
//            'type' => 'varchar',
//            'len' => 32
//        )
    ),
    'indices' => array (
        array('name' => 'syslanguagelabelspk', 'type' =>'primary', 'fields' => array('id')),
        array('name' => 'syslanguagelabel_idx', 'type' =>'unique', 'fields' => array('name')),
    ),
);

$dictionary['syslanguagetranslations'] = array (
    'table' => 'syslanguagetranslations',
    'fields' => array (
        'id' => array (
            'name' => 'id',
            'vname' => 'LBL_ID',
            'type' => 'id'
        ),
        'syslanguagelabel_id' => array (
            'name' => 'syslanguagelabel_id',
            'vname' => 'LBL_SYSLANGUAGELABEL_ID',
            'type' => 'id',
            'required' => true,
        ),
        'syslanguage' => array (
            'name' => 'syslanguage',
            'vname' => 'LBL_LANGUAGE',
            'type' => 'char',
            'len' => 5,
            'required' => true,
        ),
        'translation_default' => array (
            'name' => 'translation_default',
            'vname' => 'LBL_TRANSLATION_DEFAULT',
            'type' => 'varchar',
            'required' => true,
        ),
        'translation_short' => array (
            'name' => 'translation_short',
            'vname' => 'LBL_TRANSLATION_SHORT',
            'type' => 'varchar',
            'required' => false,
        ),
        'translation_long' => array (
            'name' => 'translation_long',
            'vname' => 'LBL_TRANSLATION_LONG',
            'type' => 'text',
            'required' => false,
        ),
    ),
    'indices' => array (
        array('name' => 'syslanguagetranslationspk', 'type' =>'primary', 'fields' => array('id')),
        array('name' => 'syslanguagetranslationlabel_idx', 'type' =>'index', 'fields' => array('syslanguagelabel_id')),
        array('name' => 'syslanguagetranslationlang_idx', 'type' =>'index', 'fields' => array('syslanguage')),
        // array('name' => 'syslanguagelabelidlang_idx', 'type' =>'unique', 'fields' => array('syslanguagelabel_id', 'syslanguage')),
    ),
);

$dictionary['syslanguagecustomlabels'] = array (
    'table' => 'syslanguagecustomlabels',
    'fields' => $dictionary['syslanguagelabels']['fields'],
    'indices' => array (
        array('name' => 'syslanguagecustomlabelspk', 'type' =>'primary', 'fields' => array('id')),
        array('name' => 'syslanguagecustomlabel_idx', 'type' =>'unique','fields' => array('name')),
    ),
);

$dictionary['syslanguagecustomtranslations'] = array (
    'table' => 'syslanguagecustomtranslations',
    'fields' => $dictionary['syslanguagetranslations']['fields'],
    'indices' => array (
        array('name' => 'syslanguagecustomtranslationspk', 'type' =>'primary', 'fields' => array('id')),
        array('name' => 'syslanguagecustomtranslationlabel_idx', 'type' =>'index', 'fields' => array('syslanguagelabel_id')),
        array('name' => 'syslanguagecustomtranslationlang_idx', 'type' =>'index', 'fields' => array('syslanguage')),
        array('name' => 'syslanguagecustomlabelidlang_idx', 'type' =>'unique', 'fields' => array('syslanguagelabel_id', 'syslanguage')),
    ),
);

