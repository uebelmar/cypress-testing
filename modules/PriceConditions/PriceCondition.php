<?php

/*
 * Copyright notice
 * 
 * (c) 2016 twentyreasons business solutions GmbH <office@twentyreasons.com>
 * 
 * All rights reserved
 */
namespace SpiceCRM\modules\PriceConditions;

use SpiceCRM\data\SugarBean;

class PriceCondition extends SugarBean
{
    public $table_name = "priceconditions";
    public $object_name = "PriceCondition";
    public $module_dir = 'PriceConditions';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_summary_text()
    {
        return $this->pricecondition_key;
    }

    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }

    /**
     * deletes all element values from the database
     */
    public function deleteElementValues()
    {
        $this->db->query("DELETE FROM priceconditionelementvalues WHERE pricecondition_id = '{$this->id}'");
    }

    public function getDeterminationStrategiesByConditionType()
    {
        $detArray = [];
        $determinations = $this->db->query("SELECT sd.*, scd.pricedetermination_index FROM syspriceconditiontypes_determinations scd , syspricedeterminations sd WHERE scd.pricedetermination_id = sd.id ORDER BY scd.pricedetermination_index WHERE scd.priceconditiontype_id = '{$this->id}'");
        while ($determination = $this->db->fetchByAssoc($determinations)) {
            $detArray[$determination['id']] = $this->getDeterminationElementsById($determination['id']);
        }
        return $detArray;
    }

    public function getDeterminationElementsById($determinationid)
    {
        $determination = $this->db->fetchByAssoc($this->db->query("SELECT * FROM syspricedeterminations WHERE id = '$determinationid'"));
        $detEntry = [
            'id' => $determination['id'],
            'ext_id' => $determination['ext_id'],
            'elements' => []
        ];
        $elements = $this->db->query("SELECT ce.* FROM syspriceconditionelements ce, syspricedeterminationelements de WHERE ce.id = de.priceconditionelement_id AND de.pricedetermination_id = '$determinationid' ORDER BY priceconditionelement_index");
        while ($element = $this->db->fetchByAssoc($elements)) {
            $detEntry['elements'][$element['id']] = [
                'id' => $element['id'],
                'name' => $element['name'],
                'ext_id' => $element['ext_id'],
                'element_length' => $element['element_length'],
            ];
        }

        return $detEntry;
    }

}
