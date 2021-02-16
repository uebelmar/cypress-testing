<?php

/**
 * 'custom_node_function' => 'filterKUNNR_example', called after the raw condition succeeded, now check what u want to check...
 * 
 * @param SAPXMLClient $client
 * @param DOMXPath $path
 * @param type $dataNode
 * @return boolean
 */
function filterKUNNR_example(SAPXMLClient $client, DOMXPath $path, $dataNode) {

    $value = $client->queryNode($path, $dataNode, 'KUNNR');
    if (empty($value)) {
        return false;
    }
    $allowed = array("0000100043");
    if (in_array($value, $allowed)) {
        return true;
    }
    return false;
}
