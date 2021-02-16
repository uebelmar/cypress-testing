<?php

/*
 * Copyright notice
 * 
 * (c) 2016 twentyreasons business solutions GmbH <office@twentyreasons.com>
 * 
 * All rights reserved
 */
namespace SpiceCRM\modules\QuestionOptions;

use SpiceCRM\data\SugarBean;

class QuestionOption extends SugarBean {

    public $table_name = "questionoptions";
    public $object_name = "QuestionOption";
    public $module_dir = 'QuestionOptions';
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

    public function save( $check_notify = false, $fts_index_bean = true ) {
        if ( !empty( $this->text )) {
            $this->name = preg_replace('#\s#u',' ', $this->text );
            if ( mb_strlen( $this->name ) > 50 ) $this->name = mb_substr( $this->name, 0, 49 ).'…';
        }
        return parent::save( $check_notify, $fts_index_bean );
    }

    public function getQuestionOptionArray( $questiontype ) {
        $resultArray = [
            "id"           => $this->id,
            "name"         => $this->name,
            "position"     => $this->position,
            "text"         => $this->text,
            "questionset_type_parameter_id" => $this->questionset_type_parameter_id
        ];
        if ( $questiontype === 'ist' ) $resultArray['description'] = $this->description; // In this field there are the possible IST-specific (sub)options.

        return $resultArray;
    }

    public function onClone()
    {
        # Add 'mappingQuestionsetTypeParameterId' to custom data (if not already exists):
        if ( !isset( $GLOBALS['cloningData']['custom']['mappingQuestionsetTypeParameterId'] )) $GLOBALS['cloningData']['custom']['mappingQuestionsetTypeParameterId'] = [];
        if ( isset( $this->questionset_type_parameter_id[0]) and !isset( $GLOBALS['cloningData']['custom']['mappingQuestionsetTypeParameterId'][$this->questionset_type_parameter_id] )) {
            $GLOBALS['cloningData']['custom']['mappingQuestionsetTypeParameterId'][$this->questionset_type_parameter_id] = create_guid();
        }
        $this->questionset_type_parameter_id = $GLOBALS['cloningData']['custom']['mappingQuestionsetTypeParameterId'][$this->questionset_type_parameter_id];
    }

}
