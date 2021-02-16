<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\includes\SugarObjects\templates\company;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\SugarObjects\templates\basic\Basic;

class Company extends Basic
{ 	
 	/**
 	 * Constructor
 	 */
    public function __construct()
 	{
 		parent::__construct();
 		$this->emailAddress = BeanFactory::getBean('EmailAddresses');
 	}
 	
 	/**
 	 * @see parent::save()
 	 */
	public function save($check_notify=false, $fts_index_bean = true)
 	{
 	    if(!empty($GLOBALS['resavingRelatedBeans']))
 	    {
 	        return parent::save($check_notify, $fts_index_bean);
 	    } 	    

    	$ori_in_workflow = empty($this->in_workflow) ? false : true;
		$this->emailAddress->handleLegacySave($this, $this->module_dir);
    	$record_id = parent::save($check_notify, $fts_index_bean);
        $override_email = array();
        if(!empty($this->email1_set_in_workflow)) {
            $override_email['emailAddress0'] = $this->email1_set_in_workflow;
        }
        if(!empty($this->email2_set_in_workflow)) {
            $override_email['emailAddress1'] = $this->email2_set_in_workflow;
        }
        if(!isset($this->in_workflow)) {
            $this->in_workflow = false;
        }
        if($ori_in_workflow === false || !empty($override_email)){
            $this->emailAddress->saveEmailAddress($this->id, $this->module_dir, $override_email,'','','','',$this->in_workflow);
        }
		return $record_id;
	}

    /**
     * a helper function to reterieve a company via an email address
     *
     * @param $email
     * @param bool $encode
     * @param bool $deleted
     * @param bool $relationships
     * @return Basic|bool|null
     */
    public function retrieve_by_email_address($email, $encode = true, $deleted = true, $relationships = true)
    {
        $email_addr = BeanFactory::getBean('EmailAddresses');
        $result = $email_addr->retrieve_by_string_fields(['email_address' => $email]);
        if($result)
        {
            $sql = "SELECT bean_id FROM email_addr_bean_rel WHERE email_address_id = '{$email_addr->id}' AND bean_module = '$this->module_dir' AND deleted = 0";
            $row = $this->db->fetchByAssoc($this->db->query($sql));
            if(!$row) return false;
            return $this->retrieve($row['bean_id'], $encode, $deleted, $relationships);
        }
        return false;
    }

 	/**
 	 * Populate email address fields here instead of retrieve() so that they are properly available for logic hooks
 	 *
 	 * @see parent::fill_in_relationship_fields()
 	 */
	public function fill_in_relationship_fields()
	{
	    parent::fill_in_relationship_fields();
	    $this->emailAddress->handleLegacyRetrieve($this);
	}

    /**
     * ensure the is_inactive flag is properly set in the index parameters
     *
     * @return array
     */
    public function add_fts_metadata()
    {
        return array(
            'is_inactive' => array(
                'type' => 'keyword',
                'search' => false,
                'enablesort' => true
            )
        );
    }

    /**
     * write is_inactive into the index
     */
    public function add_fts_fields()
    {
        return ['is_inactive' => $this->is_inactive ? '1' : '0'];
    }

}
