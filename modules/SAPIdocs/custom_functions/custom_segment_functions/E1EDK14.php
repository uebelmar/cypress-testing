<?php

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\authentication\AuthenticationController;

function E1EDK14_in(SAPIdoc $idoc, array $segment_defintion, array &$rawFields, SugarBean &$bean, &$parent = null){
    $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();

    /**

    001	Business area
    002	Sales area
    003	Delivering company code
    004	Plant in charge
    005	Delivering plant
    006	Division
    007	Distribution channel
    008	Sales organization
    009	Purchasing group
    010	Sales group
    011	Company code
    012	Order type
    013	Purchase order type
    014	Purchasing organization
    015	Billing Type
    016	Sales office
    017	Unloading Point
    018	Quotation type
    019	PO type (SD)
    020	Transaction Tax Group
    021	Sales Document Category
     */

    switch($rawFields['QUALF']){
        case '012':
            $bean->salesdoctype = $rawFields['ORGID'];
            break;
        case '008':
            $bean->_salesorg = $rawFields['ORGID'];
            break;
        case '006':
            $bean->_division = $rawFields['ORGID'];
            break;
        case '007':
            $bean->_distributionchannel = $rawFields['ORGID'];
            break;
        case '010':
            $salesRep = $db->fetchByAssoc($db->query("SELECT * FROM schsalesgroupusers WHERE salesgroup='{$rawFields['ORGID']}'"));
            $bean->assigned_user_id = $salesRep['user_id'] ?: $current_user->id;
            break;
    }

    if($bean->_salesorg && $bean->_division && $bean->_distributionchannel){

        $elementValues = ['vkorg' => $bean->_salesorg, 'vtweg' => $bean->_distributionchannel, 'spart' => $bean->_division];
        $territory = BeanFactory::getBean('SpiceACLTerritories');
        $territoryIDs[] = $territory->getTerritoryByValues('SalesDocs', $elementValues);

        $bean->spiceacl_primary_territory = $territoryIDs[0];
        $bean->spiceacl_secondary_territories = $territoryIDs;

        unset($bean->_salesorg);
        unset($bean->_division);
        unset($bean->_distributionchannel);
    }

    return true;
}
