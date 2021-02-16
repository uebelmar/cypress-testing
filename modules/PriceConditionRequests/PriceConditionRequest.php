<?php

/*
 * Copyright notice
 * 
 * (c) 2016 twentyreasons business solutions GmbH <office@twentyreasons.com>
 * 
 * All rights reserved
 */
namespace SpiceCRM\modules\PriceConditionRequests;

use SpiceCRM\data\SugarBean;

class PriceConditionRequest extends SugarBean
{
    public $table_name = "priceconditionrequests";
    public $object_name = "PriceConditionRequest";
    public $module_dir = 'PriceConditionRequests';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_summary_text()
    {
        return $this->name;
    }

    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }


}
