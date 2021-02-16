<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

/**
 * Add all global custom functions in here.
 * Customer individual functions can be added to custom/SalesPlanningContents/CustomFunctions.php
 */

use SpiceCRM\includes\database\DBManagerFactory;


/**
 *
 * Sample function
 * @param Array $nodeParams
 * @param String $date
 * @param String $duration
 * @return float
 */
function cbSample($nodeParams, $date, $duration) {
    // do something and return the determined value
    return '';
}

function cbSampleSum($nodeParams, $date, $duration) {
	// do something and return the determined value
	return 800;
}

/**
 * Translates a human readable characteristic to its guid
 *
 * @todo
 * should be replaced by a better logic if there is more time
 *
 * @param String $humanReadableCharacteristic
 * @return String $guid
 */
function translateCharacteristicToGUID($humanReadableCharacteristic, $nodeParams = array()) {
	$db = DBManagerFactory::getInstance();
    // b0869dc9-3c56-b305-c117-4e43964a2f25 = Kunde
    // d0f60b88-ee71-8094-5b34-4e429af61410 = Kundentyp
    // 80651bc9-4ccc-6794-7e97-4e43978ef142 = Warengruppe
    // _territories = Planungs-Territorien
	//BIACSICS STATIC INFORMATION TODO
    /*
    switch(strtoupper($humanReadableCharacteristic)) {
        case 'CUSTOMER':
        	$query = "SELECT id FROM salesplanningcharacteristics WHERE name = 'Kunde'";
        	$customer = $db->fetchByAssoc($db->query($query));
        	return $customer['id']; //'b0869dc9-3c56-b305-c117-4e43964a2f25';//'6a269bdb-eb6e-94f9-4d15-4e8ad793d0b1';
        case 'CUSTOMERTYPE':
        	$query = "SELECT id FROM salesplanningcharacteristics WHERE name = 'Kundentyp'";
        	$customertype = $db->fetchByAssoc($db->query($query));
        	return $customertype['id'];//'d0f60b88-ee71-8094-5b34-4e429af61410';//'6d76640b-ef3c-4ec5-985f-4e8ad6362539';
        case 'MATERIALGROUP':
        	$query = "SELECT id FROM salesplanningcharacteristics WHERE name = 'Warengruppe'";
        	$materialgroup = $db->fetchByAssoc($db->query($query));
        	return $materialgroup['id'];//'80651bc9-4ccc-6794-7e97-4e43978ef142';//;'ab14657f-9911-7386-3c5b-4e8ad68077e9';
        case 'TERRITORY':
        	return '_territories';
    }
    */
    if($humanReadableCharacteristic == 'TERRITORY')
        return '_territories';
    else
    {
        $charObj = $db->query("SELECT id FROM salesplanningcharacteristics WHERE field_reference='$humanReadableCharacteristic'");
        while($charEntry = $db->fetchByAssoc($charObj))
        {
            if(isset($nodeParams[$charEntry['id']])) return $charEntry['id'];
        }
    }

    return null;
}


if(file_exists('modules/SalesPlanningContents/CustomFunctions.php')) {
    require_once('modules/SalesPlanningContents/CustomFunctions.php');
}
