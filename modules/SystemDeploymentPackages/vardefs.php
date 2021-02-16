<?php

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['SystemDeploymentPackage'] = array(
    'table' => 'systemdeploymentpackages',
    'audited' => true,
    'fields' => array(
        'package' => array(
            'name' => 'package',
            'vname' => 'LBL_PACKAGE',
            'type' => 'varchar',
            'len' => '32',
            'comment' => 'the package technical name'
        ),
        'extensions' => array(
            'name' => 'extensions',
            'vname' => 'LBL_EXTENSIONS',
            'type' => 'varchar',
            'len' => '200',
            'comment' => 'list of backend extensions required to install the package'
        ),
        'packages' => array(
            'name' => 'packages',
            'vname' => 'LBL_PACKAGES',
            'type' => 'varchar',
            'len' => '200',
            'comment' => 'list of required other packages to be installed to be able to laod the package'
        ),
        'rpstatus' =>
            array(
                'name' => 'rpstatus',
                'vname' => 'LBL_RPSTATUS',
                'type' => 'enum',
                'len' => '10',
                'options' => 'rpstatus_dom'
            ),
        'rptype' =>
            array(
                'name' => 'rptype',
                'vname' => 'LBL_RPTYPE',
                'type' => 'enum',
                'len' => '10',
                'options' => 'rptype_dom'
            ),
        'systemdeploymentcrs' => array(
            'name' => 'systemdeploymentcrs',
            'type' => 'link',
            'relationship' => 'sdp_scr',
            'module' => 'SystemDeploymentCRs',
            'bean_name' => 'SystemDeploymentCR',
            'source' => 'non-db',
            'vname' => 'LBL_SYSTEMDEPLOYMENTCR',
        ),
        'conflict_with' =>
            array(
                'name' => 'conflict_with',
                'vname' => 'LBL_CONFLICT_WITH',
                'type' => 'text',
                'source' => 'non-db'
            ),
        'release_date' => array(
            'name' => 'release_date',
            'type' => 'datetime',
            'vname' => 'LBL_RELEASE_DATE',
        ),
        'release_version' => array(
            'name' => 'release_version',
            'type' => 'varchar',
            'len' => 10,
            'vname' => 'LBL_RELEASE_VERSION',
        ),
        'set_version' => array(
            'name' => 'set_version',
            'type' => 'bool',
            //'len' => 10,
            'vname' => 'LBL_SET_VERSION',
        ),
        'required_release_regex' => array(
            'name' => 'required_release_regex',
            'type' => 'varchar',
            'len' => 100,
            // '6\\.5\\.16\\.(20[5-9]|2[1-9][0-9])',
            'vname' => 'LBL_REQUIRED_RELEASE_REGEX',
        ),
        'package_link' =>
            array(
                'name' => 'package_link',
                'vname' => 'LBL_PACKAGE_LINK',
                'type' => 'varchar',
                'source' => 'non-db'
            ),
        'source_release_date' => array(
            'name' => 'source_release_date',
            'type' => 'datetime',
            'vname' => 'LBL_SOURCE_RELEASE_DATE',
        ),
        'source_system' => array(
            'name' => 'source_system',
            'type' => 'varchar',
            'len' => 36,
            'vname' => 'LBL_SOURCE_SYSTEM',
        ),
        'repairs' => array(
            'name' => 'repairs',
            'vname' => 'LBL_REPAIRS',
            'type' => 'multienum',
            'options' => 'systemdeploymentpackage_repair_dom'
        ),
        'repair_modules' => array(
            'name' => 'repair_modules',
            'vname' => 'LBL_REPAIR_MODULES',
            'type' => 'text'
        ),
        'software_name' => array(
            'name' => 'software_name',
            'type' => 'varchar',
            'len' => 100,
            'vname' => 'LBL_SOFTWARE_NAME',
        ),
        'software_hash' => array(
            'name' => 'software_hash',
            'type' => 'varchar',
            'len' => 32,
            'vname' => 'LBL_SOFTWARE_HASH',
        ),
        'software_version_major' => array(
            'name' => 'software_version_major',
            'type' => 'int',
            'len' => 4,
            'vname' => 'LBL_SOFTWARE_VERSION_MAJOR',
        ),
        'software_version_minor' => array(
            'name' => 'software_version_minor',
            'type' => 'int',
            'len' => 4,
            'vname' => 'LBL_SOFTWARE_VERSION_MINOR',
        ),
        'software_version_release' => array(
            'name' => 'software_version_release',
            'type' => 'int',
            'len' => 4,
            'vname' => 'LBL_SOFTWARE_VERSION_RELEASE',
        ),
    ),
    'indices' => array(
        array('name' => 'idx_systemdeploymentpackages_id_del', 'type' => 'index', 'fields' => array('id', 'deleted'),),
    ),
    'relationships' => array(),
    'optimistic_lock' => true,
);


VardefManager::createVardef('SystemDeploymentPackages', 'SystemDeploymentPackage', array('default', 'assignable'));
