<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\ContactCCDetails;

use SpiceCRM\data\SugarBean;

class ContactCCDetail extends SugarBean
{
    public $table_name = "contactccdetails";
    public $object_name = "ContactCCDetail";
    public $module_dir = "ContactCCDetails";


    function __toString()
    {
        return $this->get_summary_text();
    }

    function get_summary_text()
    {
        return $this->companycode_name;
    }
}
