<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\SAPIdocs;

use SAPXMLClient;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\Logger\LoggerManager;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\authentication\AuthenticationController;

require_once('modules/SAPIdocs/SAPXMLClient.php');
require_once('modules/SAPIdocs/Array2XML.php');

/**
 * Class SAPIdoc
 * @package
 */
class SAPIdoc extends SugarBean
{

    const custom_field_functions_path = "modules/SAPIdocs/custom_functions/custom_field_functions";
    const custom_segment_functions_path = "modules/SAPIdocs/custom_functions/custom_segment_functions";

    //Sugar vars
    public $table_name = "sapidocs";
    public $object_name = "SAPIdoc";
    public $new_schema = true;
    public $module_dir = "SAPIdocs";
    public $direction = "in";
    public $ftsOnline = false;

    /**
     * a bean for the idoc .. utilized if mutlipe head segments are defined then the bean is handled from one to the next
     * @var null
     */
    public $idocBean = null;


    public function __construct()
    {
        $db = DBManagerFactory::getInstance();
        $db->query("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

        //get name of SugarBean constructor to support older Sugar Versions
        parent::__construct();
    }


    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }

    public function get_summary_text()
    {
        return $this->name;
    }

    /**
     * return total amount of splitted segments or 1
     *
     * @param $segment
     * @param array $fields
     * @return int
     */
    public function splitIntoSegments($segment, $fields = array())
    {
        $splitting = 1;
        /**
         * $rawFields['E1BPSDTEXT'] = [];
         * $texts = explode("\n", textformat(array('wrap' => 130, 'wrap_char' => "\r\n"), html_entity_decode($bean->freetext, ENT_QUOTES)));
         * foreach($texts as $line) {
         * $rawFields['E1BPSDTEXT'][] = array(
         * '@attributes' => array('SEGMENT' => '1'),
         * 'ITM_NUMBER' => '',
         * 'TEXT_ID' => "Z006", // Z006 = Angebotskopftext
         * 'LANGU' => "DE",
         * 'TEXT_LINE' => $line,
         * );
         * }
         */
        return $splitting;
    }

    public static function getRootSegment($idoctyp, $mestyp = "")
    {
        $db = DBManagerFactory::getInstance();
//        file_put_contents('sapi.log', __FUNCTION__." on line ".__LINE__." idoctyp=".$idoctyp."\n", FILE_APPEND);

        if (empty($idoctyp)) {
            return null;
        }
        $sql = "SELECT sapidocsegments.*, sysmodules.module, sapidocsegmentrelations.segment_function "
            . "FROM sapidocsegments "
            . "INNER JOIN sapidocsegmentrelations ON sapidocsegmentrelations.segment_id = sapidocsegments.id AND sapidocsegmentrelations.deleted = 0 AND sapidocsegmentrelations.deleted = 0 "
            . "LEFT JOIN sysmodules ON sysmodules.id = sapidocsegments.sysmodule_id "
            . "WHERE sapidocsegmentrelations.idoctyp = '" . $idoctyp . "' ";
        $addwhere = "";
        if (!empty($mestyp)) {
            $addwhere .= " AND mestyp = '" . $mestyp . "' ";
        }
        $addwhere .= " AND sapidocsegmentrelations.parent_segment_id IS NULL "
            . " AND sapidocsegments.active = 1 ";
        $sql .= $addwhere;

        $sql .= " UNION ";
        $sql .= "SELECT sapidocsegments.*, syscustommodules.module, sapidocsegmentrelations.segment_function "
            . "FROM sapidocsegments "
            . "INNER JOIN sapidocsegmentrelations ON sapidocsegmentrelations.segment_id = sapidocsegments.id AND sapidocsegmentrelations.deleted = 0 "
            . "LEFT JOIN syscustommodules ON syscustommodules.id = sapidocsegments.sysmodule_id "
            . "WHERE sapidocsegmentrelations.idoctyp = '" . $idoctyp . "' ";
        $sql .= $addwhere;

//        file_put_contents('sapi.log', __FUNCTION__." on line ".__LINE__."\n", FILE_APPEND);
//        file_put_contents('sapi.log', $sql."\n", FILE_APPEND);
//        file_put_contents('sapi.log', "--------------------------------- \n", FILE_APPEND);

        $result = $db->query($sql);
        $row = $db->fetchByAssoc($result);
        if (empty($row['id'])) {
            return null;
        }
        return $row;
    }

    /**
     * loads one or multiple root segments
     *
     * @param $idoctyp
     * @param string $mestyp
     * @return array|null
     */
    public static function getRootSegments($idoctyp, $mestyp = "")
    {
        $db = DBManagerFactory::getInstance();
//        file_put_contents('sapi.log', __FUNCTION__." on line ".__LINE__." idoctyp=".$idoctyp."\n", FILE_APPEND);

        if (empty($idoctyp)) {
            return null;
        }
        $sql = "SELECT sapidocsegments.*, sysmodules.module, sapidocsegmentrelations.segment_function, sapidocsegmentrelations.segment_order, sapidocsegmentrelations.relationship_name "
            . "FROM sapidocsegments "
            . "INNER JOIN sapidocsegmentrelations ON sapidocsegmentrelations.segment_id = sapidocsegments.id AND sapidocsegmentrelations.deleted = 0 AND sapidocsegmentrelations.deleted = 0 "
            . "LEFT JOIN sysmodules ON sysmodules.id = sapidocsegments.sysmodule_id "
            . "WHERE sapidocsegmentrelations.idoctyp = '" . $idoctyp . "' ";
        $addwhere = "";
        if (!empty($mestyp)) {
            $addwhere .= " AND mestyp = '" . $mestyp . "' ";
        }
        $addwhere .= " AND sapidocsegmentrelations.parent_segment_id IS NULL "
            . " AND sapidocsegments.active = 1 ";
        $sql .= $addwhere;

        $sql .= " UNION ";
        $sql .= "SELECT sapidocsegments.*, syscustommodules.module, sapidocsegmentrelations.segment_function, sapidocsegmentrelations.segment_order, sapidocsegmentrelations.relationship_name "
            . "FROM sapidocsegments "
            . "INNER JOIN sapidocsegmentrelations ON sapidocsegmentrelations.segment_id = sapidocsegments.id AND sapidocsegmentrelations.deleted = 0 "
            . "LEFT JOIN syscustommodules ON syscustommodules.id = sapidocsegments.sysmodule_id "
            . "WHERE sapidocsegmentrelations.idoctyp = '" . $idoctyp . "' ";
        $sql .= $addwhere;

        $rows = [];
        $result = $db->query($sql);
        while ($row = $db->fetchByAssoc($result)) {
            $rowExists = false;
            foreach($rows as $thisRow){
                if($row['id'] == $thisRow['id']){
                    $rowExists = true;
                    if(empty($thisRow['module'])){
                        $thisRow['module'] = $row['module'];
                    }
                }
            }
            if(!$rowExists) {
                $rows[] = $row;
            }
        }

        usort($rows, function ($a, $b) {
            return $a['segment_order'] >= $b['segment_order'] ? 1 : -1;
        });

        return $rows;
    }

    /**
     * call a custom segment function
     *
     * @param $bean
     * @param $parent
     * @param $segment_defintion
     * @param array $rawFields
     * @return bool
     */
    private function callCustomSegmentFunction($bean, $parent, $segment_defintion, &$rawFields = array())
    {
        $response = true; // default all segment functions return valid response, if there is no return value given
        $file = isset($segment_defintion['segment_function']) ? $segment_defintion['segment_function'] : "";
        if (empty($file)) {
            return $response;
        }
        $function = $file . "_" . $this->direction;
        if (file_exists(get_custom_file_if_exists('custom/'. self::custom_segment_functions_path . '/' . $file . '.php'))) {
            include_once(get_custom_file_if_exists('custom/'.self::custom_segment_functions_path . '/' . $file . '.php'));
            if (function_exists($function)) {
                $response = $function($this, $segment_defintion, $rawFields, $bean, $parent);
            }
            return $response;
        } else if (file_exists(get_custom_file_if_exists( self::custom_segment_functions_path . '/' . $file . '.php'))) {
            include_once(get_custom_file_if_exists(self::custom_segment_functions_path . '/' . $file . '.php'));
            if (function_exists($function)) {
                $response = $function($this, $segment_defintion, $rawFields, $bean, $parent);
            }
            return $response;
        }
        return $response;
    }

    /**
     * call a custom field function
     *
     * @param SugarBean $tmp
     * @param $parent
     * @param $field_defintion
     * @param array $rawFields
     * @return bool
     */
    private function callCustomFieldFunction(SugarBean &$tmp, $parent, $field_defintion, &$rawFields = array())
    {
        $response = true; // default all field functions return valid response, if there is no return value given
        $file = isset($field_defintion['custom_field_function']) ? $field_defintion['custom_field_function'] : "";
        if (empty($file)) {
            return $response;
        }
        $function = $file . "_" . $this->direction;
        if (file_exists(get_custom_file_if_exists(self::custom_field_functions_path . '/' . $file . '.php'))) {
            include_once(get_custom_file_if_exists(self::custom_field_functions_path . '/' . $file . '.php'));
            if (function_exists($function)) {
                $response = $function($this, $tmp, $field_defintion, $rawFields, $parent);
            }
            return $response;
        }

        return $response;
    }

    public function customLogicSegmentsAndFields(&$tmp, $parent, $segment, $name, &$rawFields = array())
    {
        $db = DBManagerFactory::getInstance();

        // any segment can be just a bride between two actuall segments without being any module
        $node = $segment['sap_segment'];
//        file_put_contents('sugarcrm.log', print_r(__FUNCTION__."() on line ".__LINE__, true)."\n", FILE_APPEND);
        if (!empty($segment['parent_segment'])) {
            $node = $segment['parent_segment'] . SAPXMLClient::segment_delimiter . $node;
        }

        if ($node === $name) {
            $valid = true;
            // we have the current match found, now we can find the exact fields for that segments and trigger custom function
            $sql = "SELECT * FROM sapidocfields "
                . "INNER JOIN sapidocsegments ON sapidocsegments.id = sapidocfields.segment_id AND sapidocsegments.active = 1 "
                . "WHERE sapidocfields.segment_id = '" . $segment['id'] . "' "
                . "AND sapidocfields.active = 1 "
                . "AND (custom_field_function IS NOT NULL OR custom_field_function != '') "
                . "ORDER BY mapping_field ASC, mapping_order ASC";
            $result = $db->query($sql);
            while ($row = $db->fetchByAssoc($result)) {
                $valid = $this->callCustomFieldFunction($tmp, $parent, $row, $rawFields);
                if (!$valid) {
                    return false;
                }
            }
            if ($valid) {
                $valid = $this->callCustomSegmentFunction($tmp, $parent, $segment, $rawFields);
                if (!$valid) {
                    return false;
                }
            }
        } else {
            $sql = "SELECT sapidocsegments.*, sapidocsegmentrelations.segment_function FROM sapidocsegments "
                . "INNER JOIN sapidocsegmentrelations ON sapidocsegmentrelations.segment_id = sapidocsegments.id AND sapidocsegmentrelations.deleted = 0 "
                . "WHERE sapidocsegmentrelations.parent_segment_id = '" . $segment['id'] . "' "
                . "AND sapidocsegmentrelations.segment_id IS NOT NULL "
                . "AND sapidocsegments.active = 1";
            $result = $db->query($sql);
            while ($row = $db->fetchByAssoc($result)) {
                $row['parent_segment'] = $node;
                $valid = $this->customLogicSegmentsAndFields($tmp, $parent, $row, $name, $rawFields);
                if (!$valid) {
                    return false;
                }
            }
        }
        return true;
    }

    private function relateSegments($seed_id, $seed_module, $parent_id, $parent_module, $relationship = "")
    {
//        file_put_contents('sugarcrm.log', print_r(__FUNCTION__."() on line ".__LINE__, true)."\n", FILE_APPEND);

        $seed = BeanFactory::getBean($seed_module, $seed_id);
        if ($seed && $parent_id && $parent_module) {
            $linked_fields = $seed->get_linked_fields();
            $rels = findRelationships($seed->module_dir, $parent_module, $relationship);
            foreach ($rels as $rel) {
                foreach ($linked_fields as $linked_field => $def) {
                    if ($def['type'] == 'link' && !empty($def['relationship']) && $def['relationship'] == $rel['relationship_name']) {
                        if ($rel['relationship_type'] == 'one-to-many' || (isset($def['link_type']) && $def['link_type'] == 'one')) {
                            // many to many doenst fit to a tree setup by a fix parent
                            $seed->load_relationship($linked_field);
                            if (is_object($seed->{$linked_field})) {
                                $addtional_values = array();
                                if (!empty($rel['relationship_role_column']) && !empty($rel['relationship_role_column_value'])) {
                                    $addtional_values[$rel['relationship_role_column']] = $rel['relationship_role_column_value'];
                                }
                                $seed->{$linked_field}->add($parent_id, $addtional_values);
                                break;
                            }
                        }
                    }
                }
            }
        }
    }

    private function markRecordOutbound($bean_id, $module, $triggered_bean_id, $triggered_bean_type, $segment_id = "")
    {
        global $timedate;

        $sql = "SELECT count(*) AS total FROM sapidocoutboundrecords "
            . "WHERE bean_id = '" . $bean_id . "' "
            . "AND bean_type = '" . $module . "' "
            . "AND triggered_bean_id = '" . $triggered_bean_id . "' "
            . "AND triggered_bean_type = '" . $triggered_bean_type . "' "
            . "AND sapidoc_id = '" . $this->id . "' "
            . "AND segment_id = '" . $segment_id . "' "
            . "AND proceeded = 0";
        $result = $this->db->query($sql);
        $row = $this->db->fetchByAssoc($result);
        if ($row['total']) {
            $update = "UPDATE sapidocoutboundrecords SET "
                . "added = '" . $timedate->nowDB() . "',"
                . "deleted = 0 "
                . "WHERE bean_id = '" . $bean_id . "'"
                . "AND bean_type = '" . $module . "' "
                . "AND triggered_bean_id = '" . $triggered_bean_id . "' "
                . "AND triggered_bean_type = '" . $triggered_bean_type . "' "
                . "AND sapidoc_id = '" . $this->id . "' "
                . "AND segment_id = '" . $segment_id . "' "
                . "AND proceeded = 0";
            $this->db->query($update);
        } else {
            $insert = "INSERT INTO sapidocoutboundrecords SET "
                . "id = '" . create_guid() . "', "
                . "bean_id = '" . $bean_id . "', "
                . "bean_type = '" . $module . "', "
                . "triggered_bean_id = '" . $triggered_bean_id . "', "
                . "triggered_bean_type = '" . $triggered_bean_type . "', "
                . "sapidoc_id = '" . $this->id . "', "
                . "segment_id = '" . $segment_id . "', "
                . "deleted = 0, "
                . "proceeded = 0, "
                . "added = '" . $timedate->nowDB() . "' ";
            $this->db->query($insert);
        }
    }

    private function markRecordInbound($bean_id, $module, $segment_id = "")
    {
        global $timedate;

        $insert = "INSERT INTO sapidocinboundrecords SET "
            . "id = '" . create_guid() . "', "
            . "bean_id = '" . $bean_id . "', "
            . "bean_type = '" . $module . "', "
            . "sapidoc_id = '" . $this->id . "', "
            . "segment_id = '" . $segment_id . "', "
            . "deleted = 0, "
            . "handled = '" . $timedate->nowDB() . "' ";
        $this->db->query($insert);
    }

    private function loadCreateBean($module, $segment_id, $properties, $parent = null, $relationship = '')
    {
        $db = DBManagerFactory::getInstance();

        if (empty($segment_id) || empty($module)) {
            return null;
        }

        $seed = BeanFactory::newBean($module);
        if (empty($seed)) {
            return null;
        }
        $string_fields = array();
        $sql = "SELECT * FROM sapidocfields "
            . "INNER JOIN sapidocsegments ON sapidocsegments.id = sapidocfields.segment_id AND sapidocsegments.active = 1 "
            . "WHERE sapidocfields.segment_id = '" . $segment_id . "' "
            . "AND sapidocfields.identifier = 1 "
            . "AND sapidocfields.active = 1";
        $result = $db->query($sql);
        while ($row = $db->fetchByAssoc($result)) {

            if (!empty($row['mapping_field']) && isset($properties[$row['mapping_field']]) && isset($seed->field_defs[$row['mapping_field']])) {
                $string_fields[$row['mapping_field']] = $properties[$row['mapping_field']];
            }
        }
        if (count($string_fields)) {
            // if we have a parent a relationship
            if ($parent && !empty($relationship)) {
                $parentSeed = BeanFactory::getBean($parent['module'], $parent['record']);

                $optionalWhere = '';
                foreach ($string_fields as $field => $value) {
                    if ($optionalWhere != '') $optionalWhere .= 'AND ';
                    $optionalWhere .= "$field ='$value'";
                }

                // find the link
                $links = $parentSeed->get_linked_fields();
                $link = '';
                foreach ($links as $field) {
                    if ($field['relationship'] == $relationship) {
                        $link = $field['name'];
                        break;
                    }
                }

                $parentSeed->load_relationship($link);
                $linkedBeans = $parentSeed->get_linked_beans($link, $module, array(), 0, 1, 0, $optionalWhere);

                // if we found an obect return the one
                if (count($linkedBeans) > 0)
                    return $linkedBeans[0];

            }  else if (!empty($relationship)) {
                $parentSeed = $this->idocBean;

                $optionalWhere = '';
                foreach ($string_fields as $field => $value) {
                    if ($optionalWhere != '') $optionalWhere .= 'AND ';
                    $optionalWhere .= "$field ='$value'";
                }

                // find the link
                $links = $parentSeed->get_linked_fields();
                $link = '';
                foreach ($links as $field) {
                    if ($field['relationship'] == $relationship) {
                        $link = $field['name'];
                        break;
                    }
                }

                $parentSeed->load_relationship($link);
                $linkedBeans = $parentSeed->get_linked_beans($link, $module, array(), 0, 1, 0, $optionalWhere);

                // if we found an obect return the one
                if (count($linkedBeans) > 0)
                    return $linkedBeans[0];

            } else {
                if (!$this->idocBean) {
                    $this->idocBean = $seed;
                    $seed->retrieve_by_string_fields($string_fields);
                }
            }
        } else if (!$parent) {
            if ($this->idocBean) {
                $seed = $this->idocBean;
            }
        } else if ($parent && empty($relationship)) {
            return $parent['bean'];
        }
        return $seed;
    }

    private function callSAPIdocXMLService()
    {

        

        if (!isset(SpiceConfig::getInstance()->config['SAPIdoc'])) {
            $this->status = 'error';
            $this->log = "no SAPIdoc config defined";
            $this->save(false, $this->ftsOnline);
            return false;
        }
        if (empty($this->idoc)) {
            $this->status = 'error';
            $this->log = "idoc XML is empty";
            $this->save(false, $this->ftsOnline);
            return false;
        }
        // specify the REST web service to interact with
        $url = SpiceConfig::getInstance()->config['SAPIdoc']['host'];
        // Open a curl session for making the call
        $curl = curl_init($url);
        // Tell curl to use HTTP POST
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        // workaround for error: SSL: no alternative certificate subject name matches target host name
        if (SpiceConfig::getInstance()->config['SAPIdoc']['disable_ssl'] && SpiceConfig::getInstance()->config['SAPIdoc']['disable_ssl'] === true) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        }
        // basic authentifaction headers
        curl_setopt($curl, CURLOPT_USERPWD, trim(SpiceConfig::getInstance()->config['SAPIdoc']['user']) . ":" . trim(SpiceConfig::getInstance()->config['SAPIdoc']['password']));
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        // Tell curl not to return headers, but do return the response
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // Set the POST arguments to pass to the Sugar server
        $postArgs = html_entity_decode($this->idoc);

        //log content
        if (isset(SpiceConfig::getInstance()->config['SAPIdoc']['debug_outbound']) && SpiceConfig::getInstance()->config['SAPIdoc']['debug_outbound'] === true) {
            $log_path = ((SpiceConfig::getInstance()->config['log_dir'] == '.' || SpiceConfig::getInstance()->config['log_dir'] == './') ? 'logs/' : SpiceConfig::getInstance()->config['log_dir']);
            if (substr($log_path, -1, 1) != DIRECTORY_SEPARATOR) {
                $log_path .= DIRECTORY_SEPARATOR;
            }
            $log_path .= "sapidocs_outbound/";
            if (!file_put_contents($log_path . $this->name . ".xml", $postArgs)) {
                file_put_contents('sapi.log', __FUNCTION__ . " Could not save " . $log_path . $this->name . ".xml for debug on line " . __LINE__ . "\n", FILE_APPEND);
            }
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-type: text/xml;charset=UTF-8',
            'Content-length: ' . strlen(html_entity_decode($this->idoc))
        ));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postArgs);
        // Make the REST call, returning the result
        $response = curl_exec($curl);
        if (empty($response)) {
            $this->status = 'error';
            $this->log = "no request sent or arrived at host: " . SpiceConfig::getInstance()->config['SAPIdoc']['host'];
            $this->log .= " curl error msg=" . curl_error($curl);
            $this->save(false, $this->ftsOnline);
            return false;
        }
        $info = curl_getinfo($curl);
        curl_close($curl);
        switch ($info['http_code']) {
            case ($info['http_code'] >= 200 && $info['http_code'] < 300):
                $this->status = 'exported';
                $this->log = print_r($response, true);
                break;
            default:
                $this->status = 'error';
                $this->log = print_r($response, true);
                break;
        }
        $this->save(false, $this->ftsOnline);
        return true;
    }

    /**
     * convert the current SAPIdoc into valid sugar beans by parsing the stored raw idoc XML incoming data
     *
     * @global type $current_user
     */
    public function handleIncomingIdoc()
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();

        if (!empty($this->idoc)) {
            $client = new SAPXMLClient($this);
            $response = $client->doIncoming();
            if (empty($response)) {
                $this->status = 'error';
            } else {
                $this->status = 'imported';
            }
            $this->direction = "in";
            $this->save(false, $this->ftsOnline);
            // echo print_r($response);
            // get the root segments
            $roots = self::getRootSegments($this->idoctyp);
            foreach ($roots as $root) {
                $root['parent_segment'] = "";
                foreach ($response as &$records) {
                    $valid = true;
                    foreach ($records as $segment_id => &$record) {
                        // check if this is in the scope for the current root segment
                        $inScope = false;
                        $segmentKeys = array_keys($record['segments']);
                        foreach($segmentKeys as $segmentKey){
                            if($segmentKey == $root['sap_segment'] || strpos($segmentKey, $root['sap_segment'] . '_') === 0){
                                $inScope = true;
                            }
                        }
                        if(!$inScope) continue;

                        //$parent = null;
                        $parent = $this->findParent($response, $record['meta']['parent']);
                        $module = isset($record['meta']['module']) ? $record['meta']['module'] : "";
                        $tmp = $this->loadCreateBean($module, $record['meta']['current'], $record['properties'], $parent, $record['meta']['relationship']);
                        if (empty($tmp)) {
                            //$parent = $this->findParent($response, $record['meta']['parent']);
                            if ($parent) {
                                $tmp = BeanFactory::getBean($parent['module'], $parent['record']);
                            }
                        }

                        // first just populate all bean values straight forward
                        if ($tmp) {
                            if (empty($tmp->id)) {
                                $tmp->id = create_guid();
                                $tmp->new_with_id = true;
                            }
                            foreach ($record['properties'] as $field => $value) {
                                if (isset($tmp->field_defs[$field])) {
                                    $tmp->{$field} = $value;
                                }
                            }
                            $parent = $this->findParent($response, $record['meta']['parent']);
                        }

                        // second find possible custom functions for each registered segment / field
                        foreach ($record['segments'] as $segment => $rawFields) {
                            $valid = $this->customLogicSegmentsAndFields($tmp, $parent, $root, $segment, $rawFields);
                            if (!$valid) {
                                break;
                            }
                        }

                        if ($tmp && $valid) {
                            $_SESSION['incoming_idoc_beans'][$tmp->module_dir][] = $tmp->id;

                            // set created by and assigned user id if not set yet
                            if(empty($tmp->created_by)) $tmp->created_by = $current_user->id;
                            if(empty($tmp->assigned_user_id)) $tmp->assigned_user_id = $current_user->id;

                            if (!$tmp->save(false, $this->ftsOnline)) {
                                LoggerManager::getLogger()->fatal(print_r(__FUNCTION__ . "() error on line " . __LINE__, true) . "\n");
                            }
                            // file_put_contents('sugarcrm.log', print_r(__FUNCTION__."() error on line ".__LINE__, true)."\n", FILE_APPEND);

                            // set the created bean to the segment record
                            $record['meta']['record'] = $tmp->id;
                            $record['meta']['bean'] = $tmp;

                            if ($tmp->id) {
                                $this->markRecordInbound($tmp->id, $module, $record['meta']['current']);
                                // now relate the new created bean to its parent bean
                                if ($record['meta']['parent'] != 'root') {
                                    $parent = $this->findParent($response, $record['meta']['parent']);
                                    if ($parent) {
                                        $this->relateSegments($tmp->id, $module, $parent['record'], $parent['module'], $record['meta']['relationship']);
                                    }
                                } else if($record['meta']['relationship'] && $this->idocBean){
                                    // we have a root segmnent that carries a relationship and thus shopudl eb linked to the idocbean
                                    $this->relateSegments($tmp->id, $module, $this->idocBean->id, $this->idocBean->module_dir, $record['meta']['relationship']);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    private function findParent($response, $parent)
    {
        foreach ($response as $records) {
            foreach ($records as $record) {
                if ($record['meta']['current'] == $parent) {
                    return $record['meta'];
                }
            }
        }
        return null;
    }

    /**
     * @param SugarBean $bean
     * @param SAPIdoc $idoc
     * @param string $exportSegment the name of the wanted segment for current export
     * @param array $objects the key value pair array for direct export. will be reset, if there is any $exportSegment given
     * @return boolean
     */
    public static function rawXMLExport(SugarBean $bean, $rootRelationID, $objects = array())
    {
        $db = DBManagerFactory::getInstance();

        if (empty($rootRelationID) || empty($bean)) {
            return false;
        }

        $idoc = BeanFactory::newBean("SAPIdocs");
        $idoc->direction = "out";

        $sql = "SELECT sapidocsegmentrelations.idoctyp, sapidocsegmentrelations.mestyp, "
            . "sapidocsegments.sap_segment, sapidocsegments.id "
            . "FROM sapidocsegmentrelations "
            . "INNER JOIN sapidocsegments ON sapidocsegments.id = sapidocsegmentrelations.segment_id AND sapidocsegments.deleted = 0 "
            . "WHERE sapidocsegmentrelations.id = '" . $rootRelationID . "' "
            . "AND sapidocsegmentrelations.deleted = 0 "
            . "AND sapidocsegments.active = 1 "
            . "LIMIT 1";
        $result = $db->query($sql);
        $row = $db->fetchByAssoc($result);
        if ($row) {
            $row['module'] = $bean->module_dir;
            $row['bean_id'] = $bean->id;
            $segments = array('root' => $row, 'all' => array($row['id'] => $row));
            if (count($segments['all'])) {
                $idoc->prepareOutbound($bean, $segments, false);
            }
        } else {
            return false;
        }

        $date = new DateTime($idoc->date_entered);
        $payload = array(
            'IDOC' => array_merge(array(
                '@attributes' => array(
                    'BEGIN' => '1'
                ),
                'EDI_DC40' => array(
                    '@attributes' => array(
                        'SEGMENT' => '1'
                    ),
                    'TABNAM' => SpiceConfig::getInstance()->config['SAPIdoc']['TABNAM'],
                    'MANDT' => SpiceConfig::getInstance()->config['SAPIdoc']['MANDT'],
                    'REFMES' => $idoc->refmes,
                    'DOCNUM' => $idoc->docnum,
                    'IDOCTYP' => $idoc->idoctyp,
                    'MESTYP' => $idoc->mestyp,
                    'SNDPOR' => SpiceConfig::getInstance()->config['SAPIdoc']['RCVPOR'],
                    'SNDPRT' => SpiceConfig::getInstance()->config['SAPIdoc']['RCVPRT'],
                    'SNDPRN' => SpiceConfig::getInstance()->config['SAPIdoc']['RCVPRN'],
                    'RCVPOR' => SpiceConfig::getInstance()->config['SAPIdoc']['SNDPOR'],
                    'RCVPRT' => SpiceConfig::getInstance()->config['SAPIdoc']['SNDPRT'],
                    'RCVPRN' => SpiceConfig::getInstance()->config['SAPIdoc']['SNDPRN'],
                    'CREDAT' => $date->format('Ymd'),
                    'CRETIM' => $date->format('His'),
                    'SERIAL' => $date->format('YmdHis')
                )
            ), $objects
            )
        );

        $xml = "";
        try {
            Array2XML::init('1.0', 'UTF-8');
            $dom = Array2XML::createXML($idoc->idoctyp, $payload);
            //loads the XML to the Document
            $xml = $dom->saveXML();
            $idoc->idoc = htmlentities($xml, ENT_QUOTES, "UTF-8");
            $idoc->status = 'prepared';
            $idoc->save(false);
            $idoc->callSAPIdocXMLService();
        } catch (DOMException $e) {
            return false;
        }
        return true;
    }

    /**
     * convert the current stored sugar beans, which are tracked by idoc support, into a raw xml SAPIdoc representation
     *
     * @param array $segments
     * @return type
     * @global type $timedate
     * @global type \SpiceCRM\includes\SugarObjects\SpiceConfig::getInstance()->config
     */
    public function handleOutgoingIdoc(array $segments)
    {

        global $timedate;

        $objects = array();
        $sql = "SELECT * FROM sapidocoutboundrecords "
            . "WHERE sapidoc_id = '" . $this->id . "' "
            . "AND deleted = 0 "
            . "AND proceeded = 0";
        $result = $this->db->query($sql);
        $row = $this->db->fetchByAssoc($result);
        $bean = BeanFactory::getBean($row['bean_type'], $row['bean_id']);
        if (!empty($bean)) {
            $this->direction = "out";
            $client = new SAPXMLClient($this);
            $object = $client->doOutgoing($bean, $segments);

            if (!empty($object)) {
                $segment = key($object);
                if (!isset($objects[$segment])) {
                    $objects[$segment] = array();
                }
                $objects[$segment][] = $object[$segment][0];
                $update = "UPDATE sapidocoutboundrecords SET "
                    . "proceeded = 1,"
                    . "handled = '" . $timedate->nowDB() . "' "
                    . "WHERE bean_id = '" . $row['bean_id'] . "' "
                    . "AND bean_type = '" . $row['bean_type'] . "' "
                    . "AND sapidoc_id = '" . $this->id . "' ";
                $this->db->query($update);
            }
        }

        $date = new DateTime($this->date_entered);
        $payload = array(
            'IDOC' => array_merge(array(
                '@attributes' => array(
                    'BEGIN' => '1'
                ),
                'EDI_DC40' => array(
                    '@attributes' => array(
                        'SEGMENT' => '1'
                    ),
                    'TABNAM' => SpiceConfig::getInstance()->config['SAPIdoc']['TABNAM'],
                    'MANDT' => SpiceConfig::getInstance()->config['SAPIdoc']['MANDT'],
                    'REFMES' => $this->refmes,
                    'DOCNUM' => $this->docnum,
                    'IDOCTYP' => $this->idoctyp,
                    'MESTYP' => $this->mestyp,
                    'SNDPOR' => SpiceConfig::getInstance()->config['SAPIdoc']['RCVPOR'],
                    'SNDPRT' => SpiceConfig::getInstance()->config['SAPIdoc']['RCVPRT'],
                    'SNDPRN' => SpiceConfig::getInstance()->config['SAPIdoc']['RCVPRN'],
                    'RCVPOR' => SpiceConfig::getInstance()->config['SAPIdoc']['SNDPOR'],
                    'RCVPRT' => SpiceConfig::getInstance()->config['SAPIdoc']['SNDPRT'],
                    'RCVPRN' => SpiceConfig::getInstance()->config['SAPIdoc']['SNDPRN'],
                    'CREDAT' => $date->format('Ymd'),
                    'CRETIM' => $date->format('His'),
                    'SERIAL' => $date->format('YmdHis')
                )
            ), $objects
            )
        );

        $xml = "";
        try {
            Array2XML::init('1.0', 'UTF-8');
            $dom = Array2XML::createXML($this->idoctyp, $payload);
            //loads the XML to the Document
            $xml = $dom->saveXML();
            $this->idoc = htmlentities($xml, ENT_QUOTES, "UTF-8");
            $this->status = 'prepared';
            $this->save(false);
            $this->callSAPIdocXMLService();
        } catch (DOMException $e) {

        }
        return $xml;
    }

    public function prepareOutbound(SugarBean $seed, array $segments, $autoExport = true)
    {

        $current_user = AuthenticationController::getInstance()->getCurrentUser();

        if (!empty($segments['root']['id'])) {
            $this->idoctyp = $segments['root']['idoctyp'];
            $this->mestyp = $segments['root']['mestyp'];
            $this->name = sprintf("%014s", str_replace(array('.', ' '), '', microtime(false)));
            $this->refmes = $this->name;
            $this->docnum = $this->name;
            $this->status = 'initial';
            $this->sndpor = SpiceConfig::getInstance()->config['SAPIdoc']['RCVPOR'];
            $this->sndprt = SpiceConfig::getInstance()->config['SAPIdoc']['RCVPRT'];
            $this->sndprn = SpiceConfig::getInstance()->config['SAPIdoc']['RCVPRN'];
            $this->rcvpor = SpiceConfig::getInstance()->config['SAPIdoc']['SNDPOR'];
            $this->rcvprt = SpiceConfig::getInstance()->config['SAPIdoc']['SNDPRT'];
            $this->rcvprn = SpiceConfig::getInstance()->config['SAPIdoc']['SNDPRN'];
            $this->assigned_user_id = $current_user->id;
            $this->created_by = $current_user->id;
            $this->sap_status_code = 0;
            $this->save(false);

            $this->markRecordOutbound($segments['root']['bean_id'], $segments['root']['module'], $seed->id, $seed->module_dir, $segments['root']['id']);
            if ($autoExport) {
                $this->handleOutgoingIdoc($segments);
            }
        }
    }

    public static function bubbleExportSegmentRecords(SugarBean $seed, $segment = array(), $required_branches = true)
    {

        $db = DBManagerFactory::getInstance();
        $branches = array('root' => array(), 'all' => array());

        $loadBranches = function (SugarBean $seed, $segment, $direction = "") use (&$loadBranches, &$branches, $db, $required_branches) {
            $sql = "SELECT * FROM ( ";
            if ($direction != "down") {
                $sql .= "SELECT sapidocsegments.*, sysmoduleslist.module, sapidocsegmentrelations.segment_order, sapidocsegmentrelations.segment_function, "
                    . "sapidocsegmentrelations.relationship_name, sapidocsegmentrelations.parent_segment_id, 'up' as 'dir' "
                    . "FROM sapidocsegments "
                    . "LEFT JOIN sapidocsegmentrelations ON sapidocsegmentrelations.parent_segment_id = sapidocsegments.id AND sapidocsegmentrelations.deleted = 0 "
                    . "LEFT JOIN (select id, module FROM sysmodules union select id, module FROM syscustommodules)  AS sysmoduleslist ON sysmoduleslist.id = sapidocsegments.sysmodule_id "
                    . "WHERE sapidocsegmentrelations.segment_id = '" . $segment['id'] . "' "
                    . "AND sapidocsegments.active = 1 ";

                if ($required_branches) {
                    $sql .= " UNION ";
                }
            }
            $sql .= "SELECT sapidocsegments.*, sysmoduleslist.module, sapidocsegmentrelations.segment_order, sapidocsegmentrelations.segment_function, "
                . "sapidocsegmentrelations.relationship_name, sapidocsegmentrelations.parent_segment_id, 'down' as 'dir' "
                . "FROM sapidocsegments "
                . "LEFT JOIN sapidocsegmentrelations ON sapidocsegmentrelations.segment_id = sapidocsegments.id AND sapidocsegmentrelations.deleted = 0 "
                . "LEFT JOIN (select id, module FROM sysmodules union select id, module FROM syscustommodules)  AS sysmoduleslist ON sysmoduleslist.id = sapidocsegments.sysmodule_id "
                . "WHERE sapidocsegmentrelations.parent_segment_id = '" . $segment['id'] . "' "
                . "AND sapidocsegments.active = 1 "
                . "AND sapidocsegmentrelations.required_export = 1 ";
            $sql .= " ) AS tmp ORDER BY tmp.segment_order ASC ";


            $result = $db->query($sql);
            while ($row = $db->fetchByAssoc($result)) {
                if (!isset($branches['all'][$row['id']])) {
                    $branches['all'][$row['id']] = $row;
                    if ($row['dir'] == "up") {
                        // just traverse the tree upwards
                        if (!empty($row['module']) && !empty($row['relationship_name'])) {
                            $tmp = null;
                            $linked_fields = $seed->get_linked_fields();
                            $rels = findRelationships($seed->module_dir, $row['module'], $row['relationship_name']);
                            foreach ($rels as $rel) {
                                foreach ($linked_fields as $linked_field => $def) {
                                    if ($def['type'] == 'link' && !empty($def['relationship']) && $def['relationship'] == $rel['relationship_name']) {
                                        if ($rel['relationship_type'] == 'one-to-many' || (isset($def['link_type']) && $def['link_type'] == 'one')) {
                                            // many to many doenst fit to a tree setup by a fix parent
                                            $seed->load_relationship($linked_field);
                                            if (is_object($seed->{$linked_field})) {
                                                $list = $seed->{$linked_field}->get();
                                                $tmp = BeanFactory::newBean($row['module']);
                                                $tmp->id = $list[0];
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                            if ($tmp instanceof SugarBean) {
                                $loadBranches($tmp, $row, "up");
                            }
                        } else {
                            $loadBranches($seed, $row, "up");
                        }
                    }
                    if ($row['dir'] == "down") {
                        // just traverse the tree downwards
                        $down = true;
                        if (!empty($row['module']) && !empty($row['relationship_name'])) {
                            $down = false;
                            $linked_fields = $seed->get_linked_fields();
                            $rels = findRelationships($seed->module_dir, $row['module'], $row['relationship_name']);
                            foreach ($rels as $rel) {
                                foreach ($linked_fields as $linked_field => $def) {
                                    if ($def['type'] == 'link' && !empty($def['relationship']) && $def['relationship'] == $rel['relationship_name']) {
                                        if ($rel['relationship_type'] == 'one-to-many' || (isset($def['link_type']) && $def['link_type'] == 'one')) {
                                            // many to many doenst fit to a tree setup by a fix parent
                                            $down = true;
                                        }
                                    }
                                }
                            }
                        }
                        if ($down) {
                            $loadBranches($seed, $row, "down");
                        }
                    }
                }
            }

            // no further segments found, we must be at root level now
            // first root node come, first root node serve
            if (empty($branches['root'])) {
                $sql = "SELECT idoctyp, mestyp, segment_function "
                    . "FROM sapidocsegmentrelations "
                    . "WHERE sapidocsegmentrelations.segment_id = '" . $segment['id'] . "' "
                    . "AND sapidocsegmentrelations.parent_segment_id IS NULL "
                    . "AND sapidocsegmentrelations.deleted = 0 "
                    . "LIMIT 1";
                $result = $db->query($sql);
                $row = $db->fetchByAssoc($result);
                if ($row['idoctyp']) {
                    $segment['idoctyp'] = $row['idoctyp'];
                    $segment['mestyp'] = $row['mestyp'];
                    $segment['segment_function'] = $row['segment_function'];
                    $segment['bean_id'] = $seed->id;
                    $branches['root'] = $segment;
                    $branches['all'][$segment['id']] = $segment;
                }
            }
        };
        $loadBranches($seed, $segment);

        if ($branches['root']['id'] != $segment['id']) {
            // adding the segment that triggered the export process
            $segment['bean_id'] = $seed->id;
            $branches['all'][$segment['id']] = $segment;
        }

        return $branches;
    }

}
