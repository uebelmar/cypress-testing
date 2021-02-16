<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['MediaFile'] = array(
    'table' => 'mediafiles',
    'comment' => 'Media Files: Images, Audios, Videos, …',
    'fields' => array (
        'filetype' => array (
            'name' => 'filetype',
            'vname' => 'LBL_FILETYPE',
            'type' => 'varchar',
            'len' => 100,
            'isnull' => false,
            'required' => false
        ),
        'alttext' => array(
            'name' => 'alttext',
            'vname' => 'LBL_ALTTEXT',
            'type' => 'varchar',
            'len' => 255
        ),
        'copyright_owner' => array(
            'name' => 'copyright_owner',
            'vname' => 'LBL_COPYRIGHT_OWNER',
            'type' => 'varchar',
            'len' => 255
        ),
        'copyright_license' => array(
            'name' => 'copyright_license',
            'vname' => 'LBL_COPYRIGHT_LICENSE',
            'type' => 'varchar',
            'len' => 255
        ),
        'height' => array(
            'name' => 'height',
            'vname' => 'LBL_HEIGHT',
            'type' => 'uint'
        ),
        'width' => array(
            'name' => 'width',
            'vname' => 'LBL_WIDTH',
            'type' => 'uint'
        ),
        'filesize' => array(
            'name' => 'filesize',
            'vname' => 'LBL_FILESIZE',
            'type' => 'ulong',
            'comment' => 'Filesize in KiloBytes'
        ),
        'cdn' => array(
            'name' => 'cdn',
            'vname' => 'LBL_CDN',
            'type' => 'bool',
            'default' => 0
        ),
        'hash' => array(
            'name' => 'hash',
            'vname' => 'LBL_HASH',
            'type' => 'varchar',
            'len' => 32,
            'isnull' => false,
            'required' => false
        ),
        'mediacategory_id' => array(
            'name' => 'mediacategory_id',
            'vname' => 'LBL_MEDIACATEGORY_ID',
            'type' => 'id',
            'required' => false
        ),
        'mediacategory' => array (
            'name' => 'mediacategory',
            'vname' => 'LBL_MEDIACATEGORY',
            'type' => 'link',
            'relationship' => 'mediacategory_mediafiles',
            'source' => 'non-db',
            'module' => 'MediaCategories'
        ),
        'mediacategory_name' => array(
            'name' => 'mediacategory_name',
            'rname' => 'name',
            'id_name' => 'mediacategory_id',
            'vname' => 'LBL_MEDIACATEGORY',
            'join_name' => 'mediacategory',
            'type' => 'relate',
            'link' => 'mediacategory',
            'table' => 'mediacetegories',
            'isnull' => 'true',
            'module' => 'MediaCategories',
            'dbType' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
        ),
        'file' => array(
            'name' => 'file',
            'vname' => 'LBL_FILE',
            'type' => 'text',
            # 'required' => true,
            'source' => 'non-db',
        ),
        'thumbnail' => array(
            'name' => 'thumbnail',
            'vname' => 'LBL_THUMBNAIL',
            'type' => 'longtext'
        ),
    ),
    'relationships' => array(
        'mediacategory_mediafiles' => array(
            'lhs_module' => 'MediaCategories',
            'lhs_table' => 'mediacategories',
            'lhs_key' => 'id',
            'rhs_module' => 'MediaFiles',
            'rhs_table' => 'mediafiles',
            'rhs_key' => 'mediacategory_id',
            'relationship_type' => 'one-to-many',
        )
    ),
    'indices' => array (
        array( 'name' =>'idx_mediafiles_name', 'type' => 'index', 'fields' => array('name') ),
        array( 'name' =>'idx_mediafiles_copyright_owner', 'type' => 'index', 'fields' => array('copyright_owner') ),
        array( 'name' =>'idx_mediafiles_deleted', 'type' => 'index', 'fields' => array('deleted') ),
        array( 'name' =>'idx_mediafiles_mediacategory', 'type' => 'index', 'fields' => array('mediacategory_id') )
    )
);

VardefManager::createVardef('MediaFiles','MediaFile', array('default','assignable'));
