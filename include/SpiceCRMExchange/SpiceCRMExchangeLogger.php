<?php
namespace SpiceCRM\includes\SpiceCRMExchange;

use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\database\DBManagerFactory;

class SpiceCRMExchangeLogger
{
    const REQUEST_TYPE_CREATE    = 'create';
    const REQUEST_TYPE_READ      = 'read';
    const REQUEST_TYPE_UPDATE    = 'update';
    const REQUEST_TYPE_DELETE    = 'delete';
    const REQUEST_TYPE_REQUESTED = 'requested';
    const REQUEST_TYPE_UPDATE_PARTICIPANTS = 'update_participants';

    private $db;

    public function __construct() {
        $db = DBManagerFactory::getInstance();

        $this->db = $db;
    }

    public function logInboundRecord($externalId, $beanId = '', $beanClass = '', $responseDetails = '') {
        global $timedate;
        $sql = "INSERT INTO sysexchangeinboundrecords (id, requested_at, deleted, bean_id, bean_type, exchange_id, response_details) 
                VALUES ('" . create_guid() . "', '" . $timedate->nowDb() . "', 0, '" . $beanId ."','" . $beanClass . "', '" . $externalId . "', '" . $this->db->quote($responseDetails) . "')";

        $this->db->query($sql);
    }

    public function logOutboundRecord(SugarBean $bean, $requestType, $requestDetails = '') {
        global $timedate;
        $sql = "INSERT INTO sysexchangeoutboundrecords (id, requested_at, deleted, bean_id, bean_type, exchange_id, request_type, request_details) 
                VALUES ('" . create_guid() . "', '" . $timedate->nowDb() . "', 0, '" . $bean->id ."','" . get_class($bean) . "', '" . $bean->external_id . "', '" . $requestType . "', '" . $this->db->quote($requestDetails) . "')";

        $this->db->query($sql);
    }
}
