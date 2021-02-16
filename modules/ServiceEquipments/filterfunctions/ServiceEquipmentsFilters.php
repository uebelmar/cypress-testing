<?php
namespace SpiceCRM\modules\ServiceEquipments\filterfunctions;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;


class ServiceEquipmentsFilters{

    function getServiceEquipmentsOfServiceOrder($bean_json){
        $db = DBManagerFactory::getInstance();

        $serviceorder_id = json_decode ($bean_json)->serviceorder_id;
        $servicelocation_id = json_decode ($bean_json)->servicelocation_id;

        if($servicelocation_id || $serviceorder_id) {
            $ids = [];
            $idsObj = $db->query("SELECT s.id as id FROM serviceequipments s WHERE s.servicelocation_id = '" . $servicelocation_id . "'  AND s.deleted = 0 UNION SELECT se.serviceequipment_id as id FROM serviceorders_serviceequipments se WHERE se.serviceorder_id = '" . $serviceorder_id . "' AND se.deleted = 0");
            while ($id = $db->fetchByAssoc($idsObj)) {
                $ids[] = $id['id'];
            }
            return $ids;
        }
        return [];
    }

    function getServiceEquipmentsOfAccount( $accountId ) {
        $ids = [];
        $account = BeanFactory::getBean('Accounts', $accountId );
        if ( $account ) {
            $serviceLocations = $account->get_linked_beans('servicelocations', 'ServiceLocation');
            foreach ( $serviceLocations as $location ) {
                $serviceEquipments = $location->get_linked_beans('serviceequipments','ServiceEquipment');
                foreach ( $serviceEquipments as $v ) {
                    $ids[] = $v->id;
                }
            }
        }
        return $ids;
    }

}
