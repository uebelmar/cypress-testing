<?php

use SpiceCRM\includes\Logger\LoggerManager;

if(!defined('sugarEntry'))define('sugarEntry', true);
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/


require_once('service/v2/registry.php'); //Extend off of v2 registry

class registry_v2_1 extends registry {
	
	/**
	 * This method registers all the functions on the service class
	 *
	 */
	protected function registerFunction() {
		
		LoggerManager::getLogger()->info('Begin: registry->registerFunction');
		parent::registerFunction();
		            
	    LoggerManager::getLogger()->info('END: registry->registerFunction');
	        
		// END OF REGISTER FUNCTIONS
	}
	
	/**
	 * This method registers all the complex types
	 *
	 */
	protected function registerTypes() {
	
	    parent::registerTypes();
	    
	    $this->serviceClass->registerType(
			'link_list2',
			'complexType',
			'struct',
			'all',
			'',
			array(
			'link_list'=>array('name'=>'link_list', 'type'=>'tns:link_list'),
			)
		);
	    
		$this->serviceClass->registerType(
		    'link_lists',
			'complexType',
		   	 'array',
		   	 '',
		  	  'SOAP-ENC:Array',
			array(),
		    array(
		        array('ref'=>'SOAP-ENC:arrayType', 'wsdl:arrayType'=>'tns:link_list2[]')
		    ),
			'tns:link_list2'
		);
		
		$this->serviceClass->registerType(
		    'link_array_list',
			'complexType',
		   	 'array',
		   	 '',
		  	  'SOAP-ENC:Array',
			array(),
		    array(
		        array('ref'=>'SOAP-ENC:arrayType', 'wsdl:arrayType'=>'tns:link_value2[]')
		    ),
			'tns:link_value2'
		);
		
		$this->serviceClass->registerType(
			'link_value2',
			'complexType',
			'struct',
			'all',
			'',
			array(
			'link_value'=>array('name'=>'link_value', 'type'=>'tns:link_value'),
			)
		);
		$this->serviceClass->registerType(
			'field_list2',
			'complexType',
			'struct',
			'all',
			'',
			array(
			"field_list"=>array('name'=>'field_list', 'type'=>'tns:field_list'),
			)
		);
		$this->serviceClass->registerType(
			'entry_list2',
			'complexType',
			'struct',
			'all',
			'',
			array(
			"entry_list"=>array('name'=>'entry_list', 'type'=>'tns:entry_list'),
			)
		);	
	}
}