<?php


use SpiceCRM\data\BeanFactory;

function E1KNVVM_out(SAPIdoc $idoc, array $segment_defintion, array &$rawFields, SugarBean $bean, $parent = null)
{

    $partnerRoles = ['AG', 'RE', 'RG', 'WE'];

    $taxVKORG = array(
        '1000' => ['AT','DE','HU','PL','UA'],
        '1010' => ['AT','DE','HU','PL','UA'],
        '1020' => ['AT','DE','HU','PL','UA'],
        '1030' => ['AT','DE','HU','PL','UA'],
        '1040' => ['AT','DE','HU','PL','UA'],
        '1065' => ['AT','DE','HU','UA'],
        '1070' => ['AT','DE','HU','PL','UA'],
        '1001' => ['DE'],
        '1080' => ['DE'],
        '2000' => ['DE'],
        '2010' => ['DE'],
        '2100' => ['AT'],
        '2200' => ['RO'],
        '3500' => ['DE','PL'],
        '5200' => ['DK'],
        '5500' => ['HU']
    );

    // check if the account is new
    // if(!empty($parent->k_sap_customerid)) {
    if($GLOBALS['sapnewid'] && array_search($parent->id, $GLOBALS['sapnewid']) !== false){

        // add partner roles
        $rawFields['E1KNVPM'] = array();
        foreach($partnerRoles as $partnerRole){
            $rawFields['E1KNVPM'][] = array(
                '@attributes' => array('SEGMENT' => '1'),
                'PARVW' => $partnerRole,
                'KUNN2' => $parent->k_sap_customerid
            );
        }

        $rawFields['E1KNVIM'] = array();
        foreach($taxVKORG[$bean->vkorg] as $country) {
            $rawFields['E1KNVIM'][] = array(
                '@attributes' => array('SEGMENT' => '1'),
                'ALAND' => $country,
                'TATYP' => 'MWST',
                'TAXKD' => '1'
            );
        }

    } else {
        unset($rawFields['E1KNVIM']);
        unset($rawFields['E1KNVPM']);
    }

    return true;
}


function E1KNVVM_in(SAPIdoc $idoc, array $segment_defintion, array &$rawFields, SugarBean &$bean, &$parent = null){

    $elementValues = ['vkorg' => $rawFields['VKORG'], 'vtweg' => $rawFields['VTWEG'], 'spart' => $rawFields['SPART']];
    $territory = BeanFactory::getBean('SpiceACLTerritories');
    $territoryId = $territory->getTerritoryByValues('AccountCCDetails', $elementValues);

    return true;
}
