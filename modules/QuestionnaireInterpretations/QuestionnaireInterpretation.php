<?php

/*
 * Copyright notice
 * 
 * (c) 2016 twentyreasons business solutions GmbH <office@twentyreasons.com>
 * 
 * All rights reserved
 */
namespace SpiceCRM\modules\QuestionnaireInterpretations;

use SpiceCRM\data\SugarBean;

class QuestionnaireInterpretation extends SugarBean {

    public $table_name = "questionnaireinterpretations";
    public $object_name = "QuestionnaireInterpretation";
    public $module_dir = 'QuestionnaireInterpretations';
    public $unformated_numbers = true;

    public function __construct() {
        parent::__construct();
    }


    public function bean_implements($interface) {
        switch($interface) {
            case 'ACL': return true;
        }
        return false;
    }


    public function get_summary_text() {
        return $this->name;
    }
}
