<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\RESTManager;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\modules\SAPIdocs\SAPIdoc;
use SpiceCRM\includes\authentication\AuthenticationController;
use Slim\Routing\RouteCollectorProxy;

/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('sap', '1.0');

/**
 * restrict routes to authenticated users
 */
if(!SpiceCRM\includes\authentication\AuthenticationController::getInstance()->isAuthenticated()) return;

$RESTManager->app->group('/SAPIdoc', function (RouteCollectorProxy $group) {



    //deprecated slim v2 syntax: $app->post('', function () use ($app) {
    //slim v3
    $group->post('', function ($req, $res, $args) {

        $current_user = AuthenticationController::getInstance()->getCurrentUser();

        $GLOBALS['idocprocessing'] = true;

        $_SESSION['incoming_idoc_beans'] = array();
        $dom = new DOMDocument('1.0');
        try {

            //deprecated slim v2 reponse: $dom->loadXML($app->request->getBody());
            //slim v3: we get a SimpleXML  Object, not an xml string
            $input = $req->getParsedBody();
            //loads XML to the Document
            //deprecated slim v2: $dom->loadXML($dom);
            //slim v3
            $dom->loadXML($input->asXml());

            $path = new DOMXPath($dom);
            $position = 1;
            // xpath position starts at 1 and not at 0
            while ($node = $path->query('//IDOC[position()=' . $position . ']/EDI_DC40')->item(0)) {
                if (!$node) {
                    break;
                }
                $name = $path->query("DOCNUM", $node)->item(0)->nodeValue;
                $sapidoc = BeanFactory::getBean('SAPIdocs');
                $sapidoc = $sapidoc->retrieve_by_string_fields(array('name' => $name));
                if ($sapidoc) {
                    /*
                    $sapidoc->handleIncomingIdoc();
                    exit;
                    */
                    http_response_code(401);
                    header('HTTP/1.0 409 duplicate_document', true, 409);
                    exit;
                } else {
                    $sapidoc = BeanFactory::newBean('SAPIdocs');
                    $sapidoc->name = $name;
                    $sapidoc->status = 'initial';
                    $sapidoc->assigned_user_id = $current_user->id;
                    $sapidoc->created_by = $current_user->id;
                    foreach ($node->childNodes as $childNode) {
                        if ($childNode->nodeType != 1) {
                            continue;
                        }
                        $field = strtolower($childNode->nodeName);
                        if (isset($sapidoc->field_defs[$field])) {
                            $sapidoc->{$field} = $childNode->nodeValue;
                        }
                    }
                    $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>';
                    $xml .= '<' . $sapidoc->idoctyp . '>';
                    $xml .= $dom->saveXML($path->query('//IDOC[position()=' . $position . ']')->item(0));
                    $xml .= '</' . $sapidoc->idoctyp . '>';

                    //log content
                    if(isset(SpiceConfig::getInstance()->config['SAPIdoc']['debug_inbound']) && SpiceConfig::getInstance()->config['SAPIdoc']['debug_inbound'] === true){
                        $log_path = ( (SpiceConfig::getInstance()->config['log_dir'] == '.' || SpiceConfig::getInstance()->config['log_dir'] == './') ? 'logs/' : SpiceConfig::getInstance()->config['log_dir']);
                        $log_path.= "sapidocs_inbound/";
                        file_put_contents($log_path.$sapidoc->name.".xml", $xml);
                    }


                    $sapidoc->idoc = htmlentities($xml, ENT_QUOTES, "UTF-8");
                    $sapidoc->save(false, false);
                }
                if (!empty($sapidoc->id) && SpiceConfig::getInstance()->config['sapidocs']['processsynchronously']) {
                    $sapidoc->handleIncomingIdoc();
                }
                $position++;
            }
        } catch (DOMException $e) {
            http_response_code(401);
            header('HTTP/1.0 409 input_not_correct ' . print_r($e, true), true, 409);
            exit;
        }
        unset($_SESSION['incoming_idoc_beans']);
    });

    $group->get('', function () {
        return true;
    });

    //deprecated slim v2 syntax
//    $app->group('/:DOCNUM', function () use ($app) {
//        $app->post('/export', function ($DOCNUM) use ($app) {
    //slim v3
    $group->group('/{DOCNUM}', function (RouteCollectorProxy $group) {
        $group->post('/export', function ($req, $res, $args) {
            $db = DBManagerFactory::getInstance();

            $sapidoc = BeanFactory::getBean('SAPIdocs');

            //deprecated slim v2 syntax:  $sapidoc->retrieve_by_string_fields(array('name' => $DOCNUM));
            //slim v3
            $sapidoc->retrieve_by_string_fields(array('name' => $args['DOCNUM']));

            if (empty($sapidoc->id)) {
                http_response_code(404);
                header('HTTP/1.0 404 not found IDOC', true, 404);
                exit;
            }
            //deprecated Slim v2 syntax $app->response->headers->set('Content-Type', 'application/xml');
            //slim v3
            $res->withHeader('Content-Type', 'application/xml');


            $sql = "SELECT triggered_bean_id, triggered_bean_type "
                . "FROM sapidocoutboundrecords "
                . "AND sapidoc_id = '" . $sapidoc->id . "' "
                . "AND proceeded = 0";
            $result = $db->query($sql);
            $row = $db->fetchByAssoc($result);
            $bean = BeanFactory::getBean($row['triggered_bean_type'], $row['triggered_bean_id']);
            // available segments defined?
            $sql = "SELECT sapidocsegments.*, sysmodules.module FROM sapidocsegments "
                . "INNER JOIN sysmodules ON sysmodules.id = sapidocsegments.sysmodule_id "
                . "WHERE sysmodules.module = '" . $bean->module_dir . "' "
                . "AND sapidocsegments.active = 1
                UNION
                SELECT sapidocsegments.*, syscustommodules.module FROM sapidocsegments "
                . "INNER JOIN syscustommodules ON syscustommodules.id = sapidocsegments.sysmodule_id "
                . "WHERE syscustommodules.module = '" . $bean->module_dir . "' "
                . "AND sapidocsegments.active = 1                
                ";
            $result = $db->query($sql);
            while ($row = $db->fetchByAssoc($result)) {
                $segments = SAPIdoc::bubbleExportSegmentRecords($bean, $row);
                if (count($segments['all'])) {
                    $xml = $sapidoc->handleOutgoingIdoc($segments);
                    echo $xml;
                }
            }
        });
    });
});
