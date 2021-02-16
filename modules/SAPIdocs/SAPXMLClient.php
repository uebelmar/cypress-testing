<?php

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;

/***** SPICE-HEADER-SPACEHOLDER *****/

class SAPXMLClient
{
    const custom_field_functions_path = "modules/SAPIdocs/custom_functions/custom_field_functions";
    const custom_functions_path = "modules/SAPIdocs/custom_functions/custom_node_functions";
    const descendent_or_self = false;
    const segment_delimiter = "__";

    private $object_list = array();
    private $log = true;
    private $debug_mode = false;
    private $namespaces = array(
        'soap-env' => 'http://schemas.xmlsoap.org/soap/envelope/',
    );
    private $root_node = "";
    private $mapping = array();
    private $sapidoc = null;

    public function __construct($sapidoc)
    {
        $this->sapidoc = $sapidoc;
        $this->root_node = "//" . $sapidoc->idoctyp . "/IDOC";
        if (self::descendent_or_self) {
            // double slash (//) is the descendant-or-self axis; it is short for /descendant-or-self::node()/
            // will result in expression like: //div[@id='add']//span[@id=addone']
            // will match any entity within a valid root node, similar to wildcarding
            $this->root_node .= "/";
        }
    }

    private function sorting(array $sorting, array $fields, $prefix = "")
    {
        // now start sorting the values
        foreach ($sorting as $sort_field => $sort_defs) {
            # SORT_REGULAR - vergleiche Einträge normal (ohne die Typen zu ändern)
            # SORT_NUMERIC - vergleiche Einträge numerisch
            # SORT_STRING - vergleiche Einträge als Strings
            # SORT_LOCALE_STRING - vergleiche Einträge als Strings, basierend auf den aktuellen Locale-Einstellungen
            if (count($fields[$prefix . $sort_field]) < count($fields[$prefix . $sort_defs['related']])) {
                for ($i = count($fields[$prefix . $sort_field]); $i < count($fields[$prefix . $sort_defs['related']]); $i++) {
                    // force equal size of the sorting arrays
                    $fields[$prefix . $sort_field][$i] = null;
                }
            }
            if (count($fields[$prefix . $sort_defs['related']]) < count($fields[$prefix . $sort_field])) {
                for ($i = count($fields[$prefix . $sort_defs['related']]); $i < count($fields[$prefix . $sort_field]); $i++) {
                    // force equal size of the sorting arrays
                    $fields[$prefix . $sort_defs['related']][$i] = null;
                }
            }
            array_multisort($fields[$prefix . $sort_field], $sort_defs['dir'], $sort_defs['type'], $fields[$prefix . $sort_defs['related']]);
            // remove the null added fields again for multisort
            $fields[$prefix . $sort_field] = array_diff($fields[$prefix . $sort_field], array(null));
        }
        return $fields;
    }

    public function doIncoming($xml = "", $json = false)
    {
        if (empty($xml)) {
            $xml = rawurldecode(html_entity_decode($this->sapidoc->idoc, ENT_COMPAT | ENT_HTML401, "UTF-8"));
            $xml = str_replace('&', '&amp;', $xml);
        }

        // dirty hack to put CSPAN Tags on TDLine Objects .. tehy can come formatted with tags but not properly encoded
        $tdlineMatches = [];
        preg_match_all('/<TDLINE>(.+?)<\/TDLINE>/', $xml, $tdlineMatches);
        foreach($tdlineMatches[0] as $index => $data){
            $xml = str_replace($data, "<TDLINE><![CDATA[{$tdlineMatches[1][$index]}]]></TDLINE>", $xml);
        }


        $dom = new DOMDocument('1.0');
        try {
            //loads XML to the Document
            $dom->loadXML($xml);
            if ($this->log) {

            }
        } catch (DOMException $e) {

        }

        $this->getImportMapping();
        if ($this->mapping) {
            $this->map($dom);
            if ($json) {
                return $this->encode($this->object_list);
            }
        }
        return $this->object_list;
    }

    /**
     * loads a segemnt relationship tree in array form given by a top level sugar bean
     *
     * @param SugarBean $seed
     * @param array $segments
     * @return string
     * @global type $db
     */
    public function doOutgoing(SugarBean $seed, array $segments)
    {

        $db = DBManagerFactory::getInstance();

        $xml = "";
        if ($segments['root']['module'] != $seed->module_dir) {
            return $xml;
        }

        return $this->loadOutboundSegments($segments['root'], $seed);
    }

    /**
     * loads the outbound fields for a given Segment
     *
     * @param $segment
     * @param $seed
     * @return array
     */
    private function loadOutboundFields($segment, $seed)
    {
        $db = DBManagerFactory::getInstance();
        $sql = "SELECT sapidocfields.*  FROM sapidocfields "
            . "WHERE sapidocfields.segment_id = '" . $segment['id'] . "' "
            . "AND sapidocfields.active = 1 "
            . "AND sapidocfields.outbound = 1 "
            . "ORDER BY sap_field ASC, mapping_order ASC";
        $result = $db->query($sql);
        $fields = array();
        while ($row = $db->fetchByAssoc($result)) {

            // set the default value if one is set
            $value = $row['mapping_field_default'];
            // check if we can and should map
            $field = $row['mapping_field'];
            if ($field && isset($seed->field_defs[$field]) && $seed->{$field} != '') {
                $value = $seed->{$field};
                if ($row['value_conector'] != "" && $this->sapidoc->direction !== 'out') {
                    $values = explode($row['value_conector'], $value);
                    if (count($values) < $row['mapping_order']) {
                        $value = "";
                    } else {
                        $value = $values[$row['mapping_order'] - 1];
                    }
                }
            }

            if (!isset($fields[$row['sap_field']])) {
                $fields[$row['sap_field']] = $value;
            } else {
                if ($row['value_conector'] != "" && $value !== "") {
                    $fields[$row['sap_field']] .= $row['value_conector'] . "" . $value;
                }
            }
        }
        return $fields;
    }

    /**
     * prepares the Outbound Segment and field mapping
     *
     * @param $segment
     * @param $seed
     * @param null $parent
     * @return array[]
     */
    private function loadOutboundSegments($segment, $seed, $parent = null)
    {
        $db = DBManagerFactory::getInstance();

        $output = array($segment['sap_segment'] => array());
        $fields = $this->loadOutboundFields($segment, $seed);

        $sql = "select id, sap_segment, module, relationship_name, segment_function, required_export, segment_order, split_field, split_length from ";

        $sql .= "(SELECT sapidocsegments.id id, sapidocsegments.sap_segment sap_segment, sapidocsegments.split_field, sapidocsegments.split_length, sysmoduleslist.module, "
            . "sapidocsegmentrelations.relationship_name, sapidocsegmentrelations.segment_function, sapidocsegmentrelations.required_export, sapidocsegmentrelations.segment_order "
            . "FROM sapidocsegments "
            . "INNER JOIN sapidocsegmentrelations ON sapidocsegmentrelations.segment_id = sapidocsegments.id AND sapidocsegmentrelations.deleted = 0 "
            . "LEFT JOIN (select id, module FROM sysmodules union select id, module FROM syscustommodules)  AS sysmoduleslist ON sysmoduleslist.id = sapidocsegments.sysmodule_id "
            . "WHERE sapidocsegmentrelations.parent_segment_id = '" . $segment['id'] . "' "
            . "AND sapidocsegmentrelations.segment_id IS NOT NULL "
            . "AND sapidocsegments.active = 1 ";

        $sql .= ") AS tmp ";
        $sql .= " ORDER BY tmp.segment_order ASC";
//            file_put_contents('sapi.log', __FUNCTION__." on line ".__LINE__."\n", FILE_APPEND);
//            file_put_contents('sapi.log', $sql."\n", FILE_APPEND);
//            file_put_contents('sapi.log', "--------------------------------- \n", FILE_APPEND);

        $result = $db->query($sql);
        while ($row = $db->fetchByAssoc($result)) {
            if (!isset($segments['all'][$row['id']])) {
                // skip unwanted segments for export
                continue;
            }
            if (!empty($row['relationship_name']) && !empty($row['module'])) {
                $linked_fields = $seed->get_linked_fields();
                $rels = findRelationships($seed->module_dir, $row['module'], $row['relationship_name']);
                foreach ($rels as $rel) {
                    foreach ($linked_fields as $linked_field => $def) {
                        if ($def['type'] == 'link' && !empty($def['relationship']) && $def['relationship'] == $rel['relationship_name']) {
                            // many to many doenst fit to a tree setup by a fix parent
                            $seed->load_relationship($linked_field);
                            if (is_object($seed->{$linked_field})) {
                                //BEGIN added maretval 20180919
                                //ORIGINAL: $records = $seed->{$linked_field}->get();
                                if ($row['required_export'] > 0) //send all
                                    $records = $seed->{$linked_field}->get();
                                else //send only the one modified
                                    $records[0] = $segments['all'][$row['id']]['bean_id'];
                                //END
                                foreach ($records as $record) {
                                    $tmp = BeanFactory::getBean($row['module'], $record);
                                    if ($tmp) {
                                        $subSegmentFields = $this->loadOutboundSegments($row, $tmp, $seed);
                                        if (!isset($fields[$row['sap_segment']])) {
                                            $fields[$row['sap_segment']] = array();
                                        }
                                        foreach ($subSegmentFields[$row['sap_segment']] as $subSegment) {
                                            $fields[$row['sap_segment']][] = $subSegment;
                                        }
                                    }
                                }
                                break;
                            }
                        }
                    }
                }
            } else {
                // for non module related segments
                $subSegmentFields = $this->loadOutboundSegments($row, $seed, $parent);
                if (!isset($fields[$row['sap_segment']])) {
                    $fields[$row['sap_segment']] = array();
                }
                foreach ($subSegmentFields[$row['sap_segment']] as $subSegment) {
                    $fields[$row['sap_segment']][] = $subSegment;
                }
            }
        }

//added maretval 2017-11-02: check on seed before calling customLogicSegmentsAndFields(). $seed has to be a valid bean!
        if ($seed)
            $valid = $this->sapidoc->customLogicSegmentsAndFields($seed, $parent, $segment, $segment['sap_segment'], $fields);
        if ($valid) {
            if ($segment['split_field'] && strlen($fields[$segment['split_field']]) > $segment['split_length']) {
                $textlines = explode("\n", $fields[$segment['split_field']]);
                foreach ($textlines as $textline) {
                    $texts = explode("\n", $this->textformat(array('wrap' => $segment['split_length'], 'wrap_char' => "\r\n"), $textline));
                    foreach ($texts as $line) {
                        $fields[$segment['split_field']] = $line;
                        $output[$segment['sap_segment']][] = array_merge(array('@attributes' => array('SEGMENT' => '1')), $fields);
                    }
                }
            } else {
                $output[$segment['sap_segment']][] = array_merge(array('@attributes' => array('SEGMENT' => '1')), $fields);
            }
        }
        return $output;
    }

    private function textformat($params, $content)
    {
        if (is_null($content)) {
            return;
        }

        $style = null;
        $indent = 0;
        $indent_first = 0;
        $indent_char = ' ';
        $wrap = 80;
        $wrap_char = "\n";
        $wrap_cut = false;
        $assign = null;

        foreach ($params as $_key => $_val) {
            switch ($_key) {
                case 'style':
                case 'indent_char':
                case 'wrap_char':
                case 'assign':
                    $$_key = (string)$_val;
                    break;

                case 'indent':
                case 'indent_first':
                case 'wrap':
                    $$_key = (int)$_val;
                    break;

                case 'wrap_cut':
                    $$_key = (bool)$_val;
                    break;

                default:
                    die("textformat: unknown attribute '$_key'");
            }
        }

        if ($style == 'email') {
            $wrap = 72;
        }

        // split into paragraphs
        $_paragraphs = preg_split('![\r\n][\r\n]!', $content);
        $_output = '';

        for ($_x = 0, $_y = count($_paragraphs); $_x < $_y; $_x++) {
            if ($_paragraphs[$_x] == '') {
                continue;
            }
            // convert mult. spaces & special chars to single space
            $_paragraphs[$_x] = preg_replace(array('!\s+!', '!(^\s+)|(\s+$)!'), array(' ', ''), $_paragraphs[$_x]);
            // indent first line
            if ($indent_first > 0) {
                $_paragraphs[$_x] = str_repeat($indent_char, $indent_first) . $_paragraphs[$_x];
            }
            // wordwrap sentences
            $_paragraphs[$_x] = wordwrap($_paragraphs[$_x], $wrap - $indent, $wrap_char, $wrap_cut);
            // indent lines
            if ($indent > 0) {
                $_paragraphs[$_x] = preg_replace('!^!m', str_repeat($indent_char, $indent), $_paragraphs[$_x]);
            }
        }
        $_output = implode($wrap_char . $wrap_char, $_paragraphs);

        return $_output;
    }


    private function registerNS(DOMXPath $path)
    {
        foreach ($this->namespaces as $ns => $url) {
            $path->registerNamespace($ns, $url);
        }
    }

    private function callCustomNodeFunction(DOMXPath $path, $dataNode, $function)
    {

        $response = false;
        if (empty($function)) {
            return $response;
        }
        $params = array($this, $path, $dataNode);

        if (file_exists(get_custom_file_if_exists(self::custom_functions_path . '/' . $function . '.php'))) {
            include_once(get_custom_file_if_exists(self::custom_functions_path . '/' . $function . '.php'));
            if (function_exists($function)) {
                $response = call_user_func_array($function, $params);
            }
            return $response;
        }
        if (method_exists($this, $function)) {
            // maybe a stupid backup...
            $response = call_user_func_array(array($this . $function), $params);
        }
        return $response;
    }

    private function encode($list = array())
    {
        return json_encode($list);
    }

    private function getImportMapping()
    {

        $db = DBManagerFactory::getInstance();
        $bean_mapping = array();

        // get the root segment
        $roots = SAPIdoc::getRootSegments($this->sapidoc->idoctyp);
        foreach($roots as $root) {
                $root['parent_segment'] = "";
                $this->loadInboundSegments($root, $bean_mapping);
                //echo print_r($bean_mapping);
        }
        $this->mapping = $bean_mapping;
    }

    private function loadInboundSegments($segment, &$bean_mapping, $parent = 'root')
    {
        $db = DBManagerFactory::getInstance();
        // any segment can be just a bridge between two actual segments without being any module
        $node = $segment['sap_segment'];
        $section = $segment['id']; // identifier for the current section
        if (!empty($segment['parent_segment'])) {
            $node = $segment['parent_segment'] . self::segment_delimiter . $node;
        }
        if (!empty($segment['module'])) {
            if (!isset($bean_mapping[$section])) {
                $bean_mapping[$section] = array('segments' => array());
            }
            if (!isset($bean_mapping[$section]['segments'][$node])) {
                $bean_mapping[$section]['segments'][$node] = array(
                    'meta' => array(
                        'id' => $segment['id'],
                        'parent' => $parent,
                        'current' => $segment['id'],
                        'segment' => $segment['sap_segment'],
                        'sap_segment' => $segment['sap_segment'],
                        'segment_function' => isset($segment['segment_function']) ? $segment['segment_function'] : "",
                        'module' => isset($segment['module']) ? $segment['module'] : "",
                        'relationship' => isset($segment['relationship_name']) ? $segment['relationship_name'] : ""
                    ),
                    'mappings' => array()
                );
            }
            $bean_mapping[$section]['segments'][$node]['mappings'] = $this->loadInboundFields($segment, $node);
        } else {
            if (!isset($bean_mapping[$section])) {
                $bean_mapping[$section] = array('segments' => array());
            }
            if (!isset($bean_mapping[$section]['segments'][$node])) {
                $bean_mapping[$section]['segments'][$node] = array(
                    'meta' => array(
                        'parent' => $parent,
                        'current' => $segment['id'],
                        'id' => $segment['id'],
                        'segment' => $segment['sap_segment'],
                        'sap_segment' => $segment['sap_segment'],
                        'segment_function' => isset($segment['segment_function']) ? $segment['segment_function'] : ""
                    ),
                    'mappings' => array()
                );
            }
            $bean_mapping[$section]['segments'][$node]['mappings'] = $this->loadInboundFields($segment, $node);
        }

        $sql = "select id, sap_segment, module, relationship_name, segment_function, required_export, segment_order from ";

        $sql .= "(SELECT sapidocsegments.id id, sapidocsegments.sap_segment sap_segment, sysmoduleslist.module, sapidocsegmentrelations.relationship_name, sapidocsegmentrelations.segment_function, sapidocsegmentrelations.required_export, sapidocsegmentrelations.segment_order "
            . "FROM sapidocsegments "
            . "INNER JOIN sapidocsegmentrelations ON sapidocsegmentrelations.segment_id = sapidocsegments.id AND sapidocsegmentrelations.deleted = 0 "
            . "LEFT JOIN (select id, module FROM sysmodules union select id, module FROM syscustommodules)  AS sysmoduleslist ON sysmoduleslist.id = sapidocsegments.sysmodule_id "
            . "WHERE sapidocsegmentrelations.parent_segment_id = '" . $segment['id'] . "' "
            . "AND sapidocsegmentrelations.segment_id IS NOT NULL "
            . "AND sapidocsegments.active = 1 ";

        $sql .= ") AS tmp ";
        $sql .= " GROUP BY tmp.id";
        $sql .= " ORDER BY tmp.segment_order ASC";


//            file_put_contents('sapi.log', __FUNCTION__." on line ".__LINE__."\n", FILE_APPEND);
//            file_put_contents('sapi.log', $sql."\n", FILE_APPEND);
//            file_put_contents('sapi.log', "--------------------------------- \n", FILE_APPEND);

        $result = $db->query($sql);
        while ($row = $db->fetchByAssoc($result)) {
            $row['parent_segment'] = $node;
            // the in between segment without any module is just a placeholder but not an actual parent segment of any module
            $this->loadInboundSegments($row, $bean_mapping, $segment['id']);
        }
    }

    private function loadInboundFields($segment, $node)
    {
        $db = DBManagerFactory::getInstance();
        $sql = "SELECT sapidocfields.*, sapidocfieldconditions.condition_id  FROM sapidocfields "
            . "LEFT JOIN sapidocfieldconditions ON sapidocfieldconditions.field_id = sapidocfields.id AND sapidocfieldconditions.deleted = 0 "
            . "WHERE sapidocfields.segment_id = '" . $segment['id'] . "' "
            . "AND sapidocfields.active = 1 "
            . "AND sapidocfields.inbound = 1 "
            . "ORDER BY mapping_field ASC, mapping_order ASC";
        $result = $db->query($sql);
        $fields = array();
        while ($row = $db->fetchByAssoc($result)) {
            if (!isset($fields[$row['mapping_rule']])) {
                $fields[$row['mapping_rule']] = array();
            }
            $field = array();
            $field['field'] = array($row['mapping_field'] => $row['sap_field']);
            if ($row['value_conector'] != "") {
                $field['value_conector'] = $row['value_conector'];
            }
            if ($row['mapping_field_default'] != "") {
                $field['mapping_field_default'] = $row['mapping_field_default'];
            }
            if ($row['custom_field_function'] != "") {
                $field['custom_field_function'] = $row['custom_field_function'];
            }
            if ($row['condition_id']) {
                $field['condition'] = $this->loadInboundConditions($row, $node);
            }
            $fields[$row['mapping_rule']][] = $field;
        }
        return $fields;
    }

    private function loadInboundConditions($field, $node)
    {
        $db = DBManagerFactory::getInstance();
        $sql = "SELECT sapidocconditions.*, sapidocsegments.sap_segment FROM sapidocconditions "
            . "INNER JOIN sapidocfieldconditions ON sapidocfieldconditions.condition_id = sapidocconditions.id "
            . "LEFT JOIN sapidocsegments ON sapidocsegments.id = sapidocconditions.scope_segment_id AND sapidocsegments.active = 1 "
            . "WHERE sapidocfieldconditions.field_id = '" . $field['id'] . "' "
            . "AND sapidocconditions.active = 1";
        $result = $db->query($sql);
        $condition = array('fields' => array());
        while ($row = $db->fetchByAssoc($result)) {
            $condition['fields'][$row['condition_field']] = $row['condition_value'];
            if ($row['sap_segment']) {
                $condition['segment'] = $row['sap_segment'];
            } else {
                $condition['segment'] = $node;
            }
            if ($row['custom_node_function']) {
                $condition['custom_node_function'] = $row['custom_node_function'];
            }
        }
        return $condition;
    }

    /**
     * return to one or more given fields the value(s). each field is a representation of an XML node, which has the text value inside
     * the datanode can be either an direct XML node for querying, or it is an relative path to the wanted field
     * it always returns for a single field the first value, no matter, if one field exists more than one times inside a structure
     * it always returns a string conect of all fields inside a structure
     */
    public function queryNode(DOMXPath $path, $dataNode, $field)
    {

        $v = "";
        $nodes = array();

        if (is_array($field)) {
            foreach ($field as $vf) {
                if (is_object($dataNode)) {
                    $nodes = $path->query($vf, $dataNode);
                } else {
                    $nodes = $path->query($dataNode . '/' . $vf);
                }
                for ($i = 0; $i < $nodes->length; $i++) {
                    $node = $nodes->item($i);
                    if (!empty($node->nodeValue)) {
                        $v .= $node->nodeValue . " ";
                        break;
                    }
                }
            }
        } else {
            if (is_object($dataNode)) {
                $nodes = $path->query($field, $dataNode);
            } else {
                $nodes = $path->query($dataNode . '/' . $field);
            }

            for ($i = 0; $i < $nodes->length; $i++) {
                $node = $nodes->item($i);
                if (!empty($node->nodeValue)) {
                    $v = $node->nodeValue;
                    break;
                }
            }
        }

        $v = trim(str_replace(",", ".", $v)); // remove comma char, because its disturbing at merge and splitting for table fields such as 3,5Kg
        return $v;
    }

    /**
     * returns all node values
     *
     * @param DOMXPath $path
     * @param $dataNode
     * @return array
     */
    public function queryNodeValues(DOMXPath $path, $dataNode)
    {

        $v = [];
        $nodes = array();
        if (is_object($dataNode)) {
            $nodes = $path->query('*', $dataNode);
        } else {
            $nodes = $path->query($dataNode . '/*');
        }

        for ($i = 0; $i < $nodes->length; $i++) {
            $node = $nodes->item($i);
            if (!empty($node->nodeValue)) {
                $v[$node->nodeName] = trim(str_replace(",", ".",$node->nodeValue));
            }
        }
        return $v;
    }

    private function queryNodeForCondition(DOMXPath $path, $dataNode, $conSegment, $con_fields = array())
    {

        if ($this->debug_mode) {
            echo "new Condition:<hr>";
        }

        $check = false;
        $searchString = '';
        $conSegment = str_replace(self::segment_delimiter, "/", $conSegment);
        /**
         * "*" is a selector that matches any element (i.e. tag) -- it returns a node-set.
         * The outer [] are a conditional that operates on each individual node in that node set -- here it operates on each element in the document.
         * text() is a selector that matches all of the text nodes that are children of the context node -- it returns a node set.
         * The inner [] are a conditional that operates on each node in that node set -- here each individual text node. Each individual text node is the starting point for any path in the brackets, and can also be referred to explicitly as . within the brackets. It matches if any of the individual nodes it operates on match the conditions inside the brackets.
         * contains is a function that operates on a string. Here it is passed an individual text node (.). Since it is passed the second text node in the <Comment> tag individually, it will see the 'ABC' string and be able to match it.
         */
        // //DEBMAS05/IDOC//E1KNA1M/child::*[text()[contains(., '0000100043')] and text()[contains(., '005')]]

        foreach ($con_fields as $key => $value) {
            if ($this->debug_mode) {
                echo "checkField: " . $key . "=>" . $value . "<br>";
            }
            if (strtolower($value) == '$nodevalue') {
                // get value of the current segment and compare it later with the node fo the condition segment
                $value2 = $this->queryNode($path, $dataNode, $key);
                // avoid joines on empty tags
                if ($value == $value2 || strlen(trim($value2)) == 0) {
                    return $check;
                }
                $value = $value2;
                if ($this->debug_mode) {
                    echo "searchField: " . $key . "=>" . $value . "=>" . $value2 . "<br>";
                }
            }
            if (strlen($searchString) > 0) {
                $searchString .= ' and ';
            }
            if ($key == 'any') {
                $searchString .= $this->root_node . '/' . $conSegment . '/';
                $searchString .= 'child::*[text()[contains(., \'' . trim($value) . '\')]]';
            } else {
                $searchString .= $this->root_node . '/' . $conSegment . '/' . $key;
                $searchString .= '[contains(., \'' . trim($value) . '\')]';
            }
            if ($this->debug_mode) {
                echo "searchString: " . $searchString . "<br>";
            }
        }

        $nodes = $path->query($searchString);
        if ($nodes->length > 0) {
            $check = true;
        }
        return $check;
    }

    private function handleCondition(DOMXPath $path, $dataNode, $field_defs)
    {

        $filteredDataNodes = array();
        if (empty($field_defs['condition']['fields'])) {
            $field_defs['condition']['fields'] = array();
        }

        if ($this->queryNodeForCondition($path, $dataNode, $field_defs['condition']['segment'], $field_defs['condition']['fields'])) {
            if (isset($field_defs['condition']['custom_node_function'])) {
                if ($this->callCustomNodeFunction($path, $dataNode, $field_defs['condition']['custom_node_function']) !== false) {
                    // special function has revealed an incorrect value of the last check before accepting the node
                    $filteredDataNodes[] = $dataNode;
                }
            }
        }

        if (!count($filteredDataNodes)) {
            return null;
        }
        return $filteredDataNodes;
    }

    private function nillFieldValues($module, $field_defs, $rule, &$merges = array())
    {
        // condition failed? then lets nill the values
        $fields = array_keys($field_defs['field']);
        $prefix = $field_defs['mapping_field_prefix'];
        foreach ($fields as $field) {
            switch ($rule) {
                case 'array':
                    $this->object_list[$module][$prefix . $field] = array();
                    break;
                case 'merge':
                    $merges[$field] = array("");
                    break;
                default:
                    $this->object_list[$module][$prefix . $field] = "";
                    break;
            }
        }
    }

    private function storeRawValues(DOMXPath $path, $dataNode, $segment, &$rawValues, $all)
    {

        if (!isset($rawValues[$segment])) {
            $rawValues[$segment] = array();
        }

        $readRawValues = function ($dataNode, &$rawValues) use (&$readRawValues, &$path, &$all) {
            if ($dataNode) {
                // just get direct children of current segment node, where each child has no further child nodes
                $nodes = $path->query("child::*[not(./child::*)]", $dataNode);
                for ($i = 0; $i < $nodes->length; $i++) {
                    $node = $nodes->item($i);
                    $rawValues[$node->nodeName] = trim($node->nodeValue);
                }
                if ($all) {
                    $nodes = $path->query("child::*[(./child::*)]", $dataNode);
                    for ($i = 0; $i < $nodes->length; $i++) {
                        $node = $nodes->item($i);
                        if (!isset($rawValues[$node->nodeName])) {
                            $rawValues[$node->nodeName] = array();
                        }
                        $item = array();
                        $readRawValues($node, $item);
                        $rawValues[$node->nodeName][] = $item;
                    }
                }
            }
        };
        $readRawValues($dataNode, $rawValues[$segment]);
    }

    private function map(DOMDocument $dom = null)
    {

        if ($dom instanceof DOMDocument) {
            //create a XPath object to query the XML
            $path = new DOMXPath($dom);
            $this->registerNS($path);

            foreach ($this->mapping as $module => $settings) {
                $this->object_list[$module] = array();
                foreach ($settings['segments'] as $segment => $defs) {
                    $nodes = $path->query($this->root_node . '/' . str_replace(self::segment_delimiter, "/", $segment));
                    for ($n = 0; $n < $nodes->length; $n++) {
                        $dataNode = $nodes->item($n);
                        $filteredDataNodes = $dataNode;
                        if (!isset($this->object_list[$module][$n])) {
                            $this->object_list[$module][$n] = array(
                                'meta' => $defs['meta'],
                                'segments' => array(),
                                'properties' => array()
                            );
                        }

                        // in any case store all the raw values and make them available
                        // $this->storeRawValues($path, $dataNode, $segment, $this->object_list[$module][$n]['segments'],empty($defs['meta']['module']));
                        $this->storeRawValues($path, $dataNode, $segment, $this->object_list[$module][$n]['segments'],true );

                        //DEBMAS05/IDOC/E1KNA1M/E1KNVVM/E1KNVPM/child::*
                        $destination = &$this->object_list[$module][$n]['properties'];
                        foreach ($defs['mappings'] as $rule => $fields) {
                            switch ($rule) {
                                case 'regular':
                                    // takes the latest values
                                    for ($i = 0; $i < count($fields); $i++) {
                                        $prefix = isset($fields[$i]['mapping_field_prefix']) ? $fields[$i]['mapping_field_prefix'] : "";
                                        if (isset($fields[$i]['condition'])) {
                                            $filteredDataNodes = $this->handleCondition($path, $dataNode, $fields[$i]);
                                        }
                                        if (!$filteredDataNodes) {
                                            $this->nillFieldValues($module, $fields[$i], $rule);
                                            continue;
                                        }
                                        foreach ($fields[$i]['field'] as $field => $sap_field) {
                                            $v = $sap_field;
                                            if (!empty($v) && $v != '1') {
                                                $v = $this->queryNode($path, $dataNode, $sap_field);
                                                if (isset($fields[$i]['mapping_field_default']) && $v == "") {
                                                    $v = $fields[$i]['mapping_field_default'];
                                                }
                                            }

                                            if(!empty($fields[$i]['custom_field_function'])){
                                                $v = $this->callCustomFieldFunction($v, $fields[$i]['custom_field_function'], $this->queryNodeValues($path, $dataNode));
                                            }

                                            if (!empty($fields[$i]['value_conector']) && $destination[$prefix . $field] != "") {
                                                if ($v != "") {
                                                    $destination[$prefix . $field] .= $fields[$i]['value_conector'] . $v;
                                                }
                                            } else {
                                                $destination[$prefix . $field] = $v;
                                            }
                                            if ($this->debug_mode) {
                                                echo "VALUE:" . $destination[$prefix . $field] . "<br>";
                                            }
                                        }
                                    }
                                    break;
                                case 'array':
                                    // takes all found values into an array
                                    for ($i = 0; $i < count($fields); $i++) {
                                        $prefix = $fields[$i]['mapping_field_prefix'];
                                        if (isset($fields[$i]['condition'])) {
                                            $filteredDataNodes = $this->handleCondition($path, $dataNode, $fields[$i]);
                                        }
                                        if (!$filteredDataNodes) {
                                            $this->nillFieldValues($module, $fields[$i], $rule);
                                            continue;
                                        }
                                        foreach ($fields[$i]['field'] as $field => $sap_field) {
                                            $v = $sap_field;
                                            if ($v != '1') {
                                                $v = $this->queryNode($path, $dataNode, $sap_field);
                                                if (isset($fields[$i]['mapping_field_default']) && $v == "") {
                                                    $v = $fields[$i]['mapping_field_default'];
                                                }
                                            }
                                            $destination[$prefix . $field][] = $v;
                                        }
                                    }
                                    break;
                                case 'exclusive':
                                    // takes all values of one (the first) winning side
                                    $exclusive = false;
                                    for ($j = 0; $j < count($fields); $j++) {
                                        if ($exclusive) {
                                            break;
                                        }
                                        $ex_fields = $fields[$j];
                                        for ($i = 0; $i < count($ex_fields); $i++) {
                                            $prefix = $ex_fields[$i]['mapping_field_prefix'];
                                            if (isset($ex_fields[$i]['condition'])) {
                                                $filteredDataNodes = $this->handleCondition($path, $dataNode, $ex_fields[$i]);
                                            }
                                            if (!$filteredDataNodes) {
                                                $this->nillFieldValues($module, $fields[$i], $rule);
                                                continue;
                                            }
                                            foreach ($ex_fields[$i]['field'] as $field => $sap_field) {
                                                $v = $sap_field;
                                                if ($v != '1') {
                                                    $v = $this->queryNode($path, $dataNode, $sap_field);
                                                    if (isset($ex_fields[$i]['mapping_field_default']) && $v == "") {
                                                        $v = $ex_fields[$i]['mapping_field_default'];
                                                    }
                                                }

                                                if (!empty($fields[$i]['value_conector']) && $destination[$prefix . $field] != "") {
                                                    if ($v != "") {
                                                        $destination[$prefix . $field] .= $fields[$i]['value_conector'] . $v;
                                                    }
                                                } else {
                                                    $destination[$prefix . $field] = $v;
                                                }
                                                if ($this->debug_mode) {
                                                    echo "VALUE:" . $destination[$prefix . $field] . "<br>";
                                                }
                                            }

                                            // only one side can be used for exclusive or, which comes first and is true, has won
                                            $exclusive = true;
                                        }
                                    }
                                    break;
                                case 'merge':
                                    // merges all found values into unique representation
                                    $merges = array();
                                    for ($i = 0; $i < count($fields); $i++) {
                                        $prefix = $fields[$i]['mapping_field_prefix'];
                                        if (isset($fields[$i]['condition'])) {
                                            $filteredDataNodes = $this->handleCondition($path, $dataNode, $fields[$i]);
                                        }
                                        if (!$filteredDataNodes) {
                                            $this->nillFieldValues($module, $fields[$i], $rule, $merges);
                                            continue;
                                        }
                                        foreach ($fields[$i]['field'] as $field => $sap_field) {
                                            if (!is_array($merges[$prefix . $field])) {
                                                $merges[$prefix . $field] = array();
                                            }
                                            $v = $sap_field;
                                            if ($v != '1') {
                                                $v = $this->queryNode($path, $dataNode, $sap_field);
                                                if (isset($fields[$i]['mapping_field_default']) && $v == "") {
                                                    $v = $fields[$i]['mapping_field_default'];
                                                }
                                            }

                                            if (!in_array($v, $merges[$prefix . $field])) {
                                                $merges[$prefix . $field][] = $v;
                                            }
                                        }
                                    }

                                    foreach ($merges as $field => $entries) {
                                        $destination[$field] = implode(', ', $entries);
                                    }
                                    break;
                            }
                        }
                    }
                }
            }
        }
    }


    private function callCustomFieldFunction($v, $custom_field_function, $rawFields = array())
    {
        $function = $custom_field_function . "_map";
        if (file_exists(get_custom_file_if_exists(self::custom_field_functions_path . '/' . $custom_field_function . '.php'))) {
            include_once(get_custom_file_if_exists(self::custom_field_functions_path . '/' . $custom_field_function . '.php'));
            if (function_exists($function)) {
                $response = $function($rawFields);
            }
            return $response;
        }

        return '';
    }

}
