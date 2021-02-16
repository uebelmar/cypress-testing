<?php
if(!defined('sugarEntry'))define('sugarEntry', true);
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/


/**
 * This class is an implemenatation class for all the rest services
 */
require_once('service/core/SugarWebServiceImpl.php');

class SugarWebServiceImplv2_1 extends SugarWebServiceImpl
{
	/**
	 * Retrieve a list of beans.  This is the primary method for getting list of SugarBeans from Sugar using the SOAP API.
	 *
	 * @param String $session -- Session ID returned by a previous call to login.
	 * @param String $module_name -- The name of the module to return records from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
	 * @param String $query -- SQL where clause without the word 'where'
	 * @param String $order_by -- SQL order by clause without the phrase 'order by'
	 * @param integer $offset -- The record offset to start from.
	 * @param Array  $select_fields -- A list of the fields to be included in the results. This optional parameter allows for only needed fields to be retrieved.
	 * @param Array $link_name_to_fields_array -- A list of link_names and for each link_name, what fields value to be returned. For ex.'link_name_to_fields_array' => array(array('name' =>  'email_addresses', 'value' => array('id', 'email_address', 'opt_out', 'primary_address')))
	 * @param integer $max_results -- The maximum number of records to return.  The default is the sugar configuration value for 'list_max_entries_per_page'
	 * @param integer $deleted -- false if deleted records should not be include, true if deleted records should be included.
	 * @return Array 'result_count' -- integer - The number of records returned
	 *               'next_offset' -- integer - The start of the next page (This will always be the previous offset plus the number of rows returned.  It does not indicate if there is additional data unless you calculate that the next_offset happens to be closer than it should be.
	 *               'entry_list' -- Array - The records that were retrieved
	 *	     		 'relationship_list' -- Array - The records link field data. The example is if asked about accounts email address then return data would look like Array ( [0] => Array ( [name] => email_addresses [records] => Array ( [0] => Array ( [0] => Array ( [name] => id [value] => 3fb16797-8d90-0a94-ac12-490b63a6be67 ) [1] => Array ( [name] => email_address [value] => hr.kid.qa@example.com ) [2] => Array ( [name] => opt_out [value] => 0 ) [3] => Array ( [name] => primary_address [value] => 1 ) ) [1] => Array ( [0] => Array ( [name] => id [value] => 403f8da1-214b-6a88-9cef-490b63d43566 ) [1] => Array ( [name] => email_address [value] => kid.hr@example.name ) [2] => Array ( [name] => opt_out [value] => 0 ) [3] => Array ( [name] => primary_address [value] => 0 ) ) ) ) )
	 * @exception 'SoapFault' -- The SOAP error, if any
	 */
	public function get_entry_list($session, $module_name, $query, $order_by,$offset, $select_fields, $link_name_to_fields_array, $max_results, $deleted )
	{
		$result = parent::get_entry_list($session, $module_name, $query, $order_by,$offset, $select_fields, $link_name_to_fields_array, $max_results, $deleted );
		if(empty($result)) {
		    return null;
		}
		$relationshipList = $result['relationship_list'];
		$returnRelationshipList = array();
		foreach($relationshipList as $rel){
			$link_output = array();
			foreach($rel as $row){
				$rowArray = array();
				foreach($row['records'] as $record){
					$rowArray[]['link_value'] = $record;
				}
				$link_output[] = array('name' => $row['name'], 'records' => $rowArray);
			}
			$returnRelationshipList[]['link_list'] = $link_output;
		}
		$result['relationship_list'] = $returnRelationshipList;
		return $result;
	} // fn
}
