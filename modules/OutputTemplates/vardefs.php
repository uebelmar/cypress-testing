<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['OutputTemplate'] = array(
    'table' => 'outputtemplates',
    'comment' => 'Templates used to output something to .pdf or so...',
    'fields' => array(
        'header' => array(
            'name' => 'header',
            'vname' => 'LBL_HEADER',
            'type' => 'html',
            'comment' => 'The html template header',
        ),
        'body_spb' => array(
            'name' => 'body_spb',
            'vname' => 'LBL_BODY_SPB',
            'type' => 'json',
            'dbType' => 'text',
            'comment' => 'save the json structure of the page builder'
        ),
        'body' => array(
            'name' => 'body',
            'vname' => 'LBL_CONTENT',
            'type' => 'html',
            'comment' => 'The html template body itself',
            'stylesheet_id_field' => 'stylesheet_id',
        ),
        'footer' => array(
            'name' => 'footer',
            'vname' => 'LBL_FOOTER',
            'type' => 'html',
            'comment' => 'The html template footer'
        ),
        'stylesheet_id' => array(
            'name' => 'stylesheet_id',
            'vname' => 'LBL_STYLE',
            'type' => 'varchar',
            'len' => 36,
        ),
        'type' => array(
            'name' => 'type',
            'vname' => 'LBL_TYPE',
            'type' => 'enum',
            'len' => 20,
            'required' => false,
            'reportable'=> false,
            'options' => 'output_template_types',
            'comment' => 'Type of the template'
        ),
        'language' => array(
            'name' => 'language',
            'vname' => 'LBL_LANGUAGE',
            'type' => 'language',
            'dbtype' => 'varchar',
            'len' => 10,
            'required' => true,
            'comment' => 'Language used by the template'
        ),
        'module_name' => array(
            'name' => 'module_name',
            'vname' => 'LBL_MODULE',
            'type' => 'enum',
            'len' => 36,
            'required' => true,
            'options' => 'modules',
            'comment' => 'The module/bean used for the template'
        ),
        'bean_id' => array(
            'name' => 'bean_id',
            'type' => 'varchar',
            'len' => 36,
            'source' => 'non-db',
        ),
        'bean' => array(
            'name' => 'bean',
            'type' => 'text',
            'source' => 'non-db',
        ),
        'page_size' => array(
            'name' => 'page_size',
            'vname' => 'LBL_PAGE_SIZE',
            'type' => 'enum',
            'len' => 5,
            'required' => true,
            'default' => 'A4',
            'options' => 'page_sizes_dom',
        ),
        'page_orientation' => array(
            'name' => 'page_orientation',
            'vname' => 'LBL_PAGE_ORIENTATION',
            'type' => 'enum',
            'len' => 1,
            'default' => 'P',
            'required' => true,
            'options' => 'page_orientation_dom',
        ),
        'margin_left' => array(
            'name' => 'margin_left',
            'vname' => 'LBL_MARGIN_LEFT',
            'type' => 'int'
        ),
        'margin_top' => array(
            'name' => 'margin_top',
            'vname' => 'LBL_MARGIN_TOP',
            'type' => 'int'
        ),
        'margin_right' => array(
            'name' => 'margin_right',
            'vname' => 'LBL_MARGIN_RIGHT',
            'type' => 'int'
        ),
        'margin_bottom' => array(
            'name' => 'margin_bottom',
            'vname' => 'LBL_MARGIN_BOTTOM',
            'type' => 'int'
        ),
        'public_name' => array(
            'name' => 'public_name',
            'vname' => 'LBL_PUBLIC_NAME',
            'type' => 'varchar',
            'len' => '255',
            'comment' => 'Name of the document, in case it published as file (to foreign persons).'
        )
    ),
    'indices' => array(
/* no duplication handling possible...
        array(
            'name' => 'idx_output_template_name',
            'type'=> 'unique',
            'fields'=> array('name','deleted','language')
        ),
*/
    ),
    'relationships' => array(

    ),
);
//BEGIN PHP7.1 compatibility: avoid PHP Fatal error:  Uncaught Error: Cannot use string offset as an array
global $dictionary;
//END

VardefManager::createVardef('OutputTemplates','OutputTemplate', array('default', 'assignable'));
