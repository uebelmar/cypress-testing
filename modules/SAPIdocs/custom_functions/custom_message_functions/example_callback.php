<?php

use SpiceCRM\includes\database\DBManagerFactory;

/**
 * 'custom_message_function' => 'example_callback', called after the requested message has been stored
 * 
 * @global type $db
 * @param DOMXPath $path
 * @param DOMNode $dataNode
 * @param SAPIdoc $sapidoc
 * @param type $received_id
 * @return type
 */
function example_callback(DOMXPath $path, DOMNode $dataNode, SAPIdoc $sapidoc, $received_id) {

    $db = DBManagerFactory::getInstance();

    $value = $path->query("Number", $dataNode)->item(0)->nodeValue;
    
    $sql = "SELECT * FROM sapidocreceivedmessages WHERE id = '" . $received_id . "'";
    $result = $db->query($sql);
    $row = $db->fetchByAssoc($result);
    return $row['id'];
}
