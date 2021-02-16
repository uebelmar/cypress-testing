<?php

use SpiceCRM\includes\Logger\LoggerManager;

if(!defined('sugarEntry'))define('sugarEntry', true);
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/


require_once('service/core/REST/SugarRest.php');

/**
 * This class is a serialize implementation of REST protocol
 * @api
 */
class SugarRestSerialize extends SugarRest{

	/**
	 * It will serialize the input object and echo's it
	 *
	 * @param array $input - assoc array of input values: key = param name, value = param type
	 * @return String - echos serialize string of $input
	 */
	function generateResponse($input){
		ob_clean();
		if (isset($this->faultObject)) {
			$this->generateFaultResponse($this->faultObject);
		} else {
			echo serialize($input);
		}
	} // fn

	/**
	 * This method calls functions on the implementation class and returns the output or Fault object in case of error to client
	 *
	 * @return unknown
	 */
	function serve(){
		LoggerManager::getLogger()->info('Begin: SugarRestSerialize->serve');
		$data = !empty($_REQUEST['rest_data'])? $_REQUEST['rest_data']: '';
		if(empty($_REQUEST['method']) || !method_exists($this->implementation, $_REQUEST['method'])){
			$er = new SoapError();
			$er->set_error('invalid_call');
			$this->fault($er);
		}else{
			$method = $_REQUEST['method'];
			$data = unserialize(from_html($data));
			if(!is_array($data))$data = array($data);
			LoggerManager::getLogger()->info('End: SugarRestSerialize->serve');
			return call_user_func_array(array( $this->implementation, $method),$data);
		} // else
	} // fn

	/**
	 * This function sends response to client containing error object
	 *
	 * @param SoapError $errorObject - This is an object of type SoapError
	 * @access public
	 */
	function fault($errorObject){
		$this->faultServer->faultObject = $errorObject;
	} // fn

	function generateFaultResponse($errorObject){
		$error = $errorObject->number . ': ' . $errorObject->name . '<br>' . $errorObject->description;
		LoggerManager::getLogger()->error($error);
		ob_clean();
		echo serialize($errorObject);
	} // fn

} // clazz
