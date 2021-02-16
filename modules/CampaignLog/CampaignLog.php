<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\CampaignLog;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\database\DBManagerFactory;



class CampaignLog extends SugarBean {

    var $table_name = 'campaign_log';
    var $object_name = 'CampaignLog';
    var $module_dir = 'CampaignLog';


    function __construct() {
        
        parent::__construct();

    }

    function fill_in_additional_detail_fields()
    {
        parent::fill_in_additional_detail_fields();

        // fill in target_name
        $target = BeanFactory::getBean($this->target_type, $this->target_id);
        $this->target_name = $target->get_summary_text();
    }


    function retrieve_email_address($trgt_id = ''){
        $return_str = '';
        if(!empty($trgt_id)){
            $qry  = " select eabr.primary_address, ea.email_address";
            $qry .= " from email_addresses ea ";
            $qry .= " Left Join email_addr_bean_rel eabr on eabr.email_address_id = ea.id ";
            $qry .= " where eabr.bean_id = '{$trgt_id}' ";
            $qry .= " and ea.deleted = 0 ";
            $qry .= " and eabr.deleted = 0" ;
            $qry .= " order by primary_address desc ";

            $result=$this->db->query($qry);
            $row=$this->db->fetchByAssoc($result);

            if (!empty($row['email_address'])){
                $return_str = $row['email_address'];
            }
        }
        return $return_str;
    }




    //this function is called statically by the campaign_log subpanel.
    function get_related_name($related_id, $related_type) {
        global $locale;
        $db= DBManagerFactory::getInstance();
        if ($related_type == 'Emails') {
            $query="SELECT name from emails where id='$related_id'";
            $result=$db->query($query);
            $row=$db->fetchByAssoc($result);
            if ($row != null) {
                return $row['name'];
            }
        }
        if ($related_type == 'Contacts') {
            $query="SELECT first_name, last_name from contacts where id='$related_id'";
            $result=$db->query($query);
            $row=$db->fetchByAssoc($result);
            if ($row != null) {
                return $full_name = $locale->getLocaleFormattedName($row['first_name'], $row['last_name']);
            }
        }
        if ($related_type == 'Leads') {
            $query="SELECT first_name, last_name from leads where id='$related_id'";
            $result=$db->query($query);
            $row=$db->fetchByAssoc($result);
            if ($row != null) {
                return $full_name = $locale->getLocaleFormattedName($row['first_name'], $row['last_name']);
            }
        }
        if ($related_type == 'Prospects') {
            $query="SELECT first_name, last_name from prospects where id='$related_id'";
            $result=$db->query($query);
            $row=$db->fetchByAssoc($result);
            if ($row != null) {
                return $full_name = $locale->getLocaleFormattedName($row['first_name'], $row['last_name']);
            }
        }
        if ($related_type == 'CampaignTrackers') {
            $query="SELECT tracker_url from campaign_trkrs where id='$related_id'";
            $result=$db->query($query);
            $row=$db->fetchByAssoc($result);
            if ($row != null) {
                return $row['tracker_url'] ;
            }
        }
        if ($related_type == 'Accounts') {
            $query="SELECT name from accounts where id='$related_id'";
            $result=$db->query($query);
            $row=$db->fetchByAssoc($result);
            if ($row != null) {
                return $row['name'];
            }
        }
		return $related_id.$related_type;
	}

	static function setStatus($id,$status){
        $db = DBManagerFactory::getInstance();
        $db->query("UPDATE campaign_log SET activity_type = '$status', activity_date = NOW() WHERE id = '$id'");
    }
}
