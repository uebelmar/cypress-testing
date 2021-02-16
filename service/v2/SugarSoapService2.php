<?php

use SpiceCRM\includes\Logger\LoggerManager;

if(!defined('sugarEntry'))define('sugarEntry', true);
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/


/**
 * This is a service class for version 2
 */
require_once('service/core/NusoapSoap.php');
class SugarSoapService2 extends NusoapSoap{
		
	/**
	 * This method registers all the functions which you want to be available for SOAP.
	 *
	 * @param array $excludeFunctions - All the functions you don't want to register
	 */
	public function register($excludeFunctions = array()){
		LoggerManager::getLogger()->info('Begin: SugarSoapService2->register');
		$this->excludeFunctions = $excludeFunctions;
		$registryObject = new $this->registryClass($this);
		$registryObject->register();
		$this->excludeFunctions = array();
		LoggerManager::getLogger()->info('End: SugarSoapService2->register');
	} // fn
			
} // clazz
?>
