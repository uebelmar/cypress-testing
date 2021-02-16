<?php

/*
 * Copyright notice
 * 
 * (c) 2016 twentyreasons business solutions GmbH <office@twentyreasons.com>
 * 
 * All rights reserved
 */
namespace SpiceCRM\modules\LandingPages;

use SpiceCRM\data\SugarBean;

class LandingPage extends SugarBean {

    public $table_name = 'landingpages';
    public $object_name = 'LandingPage';
    public $module_dir = 'LandingPages';

    public function __construct() {
        parent::__construct();
    }

    function get_summary_text()
    {
        return $this->name;
    }

    function bean_implements($interface){
        switch($interface){
            case 'ACL':return true;
        }
        return false;
    }

    function save($check_notify = false, $fts_index_bean = true)
    {
        if ( $this->content_type === 'questionnaire' ) {
            $this->content = '';
            $this->module_name = '';
            $this->handlerclass = '';
        }
        return parent::save($check_notify, $fts_index_bean);
    }

}
