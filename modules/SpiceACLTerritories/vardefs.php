<?php

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary ['SpiceACLTerritory'] = array(
    'table' => 'spiceaclterritories',
    'comment' => 'Kommentar',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'id',
            'required' => true,
            'reportable' => false
        ),
        'territorytype_id' => array(
            'name' => 'territorytype_id',
            'type' => 'id',
            'required' => true,
            'reportable' => false
        ),
        'inactive' => array(
            'name' => 'inactive',
            'type' => 'bool',
            'default' => false,
            'reportable' => false
        ),
        'usagecount' => array(
            'name' => 'usagecount',
            'type' => 'int',
            'source' => 'non-db'
        ),
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'varchar',
            'len' => 35,
            'unified_search' => true,
            'required' => true,
            'importable' => 'required'
        )
    ),
    'indices' => array(
    )
);

VardefManager::createVardef('SpiceACLTerritories', 'SpiceACLTerritory', array('default', 'assignable'));

// add fields for all objectelement to have them available on the object in the Reporter
/*
$db = \SpiceCRM\includes\database\DBManagerFactory::getInstance();
if ($db != null) {
    $objectElementsObj = $db->query("SELECT * FROM korgobjectelements");
    while ($thisObjElement = $db->fetchByAssoc($objectElementsObj)) {
        $dictionary ['KOrgObject']['fields']['Element_' . $thisObjElement['name']] = array(
            'name' => 'Element_' . $thisObjElement['name'],
            'vname' => $thisObjElement['name'],
            'type' => 'kreporter',
            'kreporttype' => 'enum',
            'options' => 'koe' . $thisObjElement['id'] . '_dom',
            'source' => 'non-db',
            'eval' => 'SELECT elementvalue FROM korgobjects_korgooe WHERE korgobject_id={t}.id AND korgobjectelement_id=\'' . $thisObjElement['id'] . '\''
        );
        $dictionary ['KOrgObject']['fields']['Element_' . $thisObjElement['name'] . '_raw'] = array(
            'name' => 'Element_' . $thisObjElement['name'] . '_raw',
            'vname' => $thisObjElement['name'] . ' raw',
            'type' => 'kreporter',
            'source' => 'non-db',
            'eval' => 'SELECT elementvalue FROM korgobjects_korgooe WHERE korgobject_id={t}.id AND korgobjectelement_id=\'' . $thisObjElement['id'] . '\''
        );
    }
}
*/
