<?php

use SpiceCRM\includes\Logger\LoggerManager;

if(!defined('sugarEntry'))define('sugarEntry', true);
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/


require_once('service/core/REST/SugarRestSerialize.php');

/**
 * This class is a JSON implementation of REST protocol
 * @api
 */
class SugarRestJSON extends SugarRestSerialize{

	/**
	 * It will json encode the input object and echo's it
	 *
	 * @param array $input - assoc array of input values: key = param name, value = param type
	 * @return String - echos json encoded string of $input
	 */
	function generateResponse($input){
		ob_clean();
		header('Content-Type: application/json; charset=UTF-8');
		if (isset($this->faultObject)) {
			$this->generateFaultResponse($this->faultObject);
		} else {
			// JSONP support
			if ( isset($_GET["jsoncallback"]) ) {
				echo $_GET["jsoncallback"] . "(";
			}
			echo json_encode($input);
			if ( isset($_GET["jsoncallback"]) ) {
				echo ")";
			}
		}
	} // fn

	/**
	 * This method calls functions on the implementation class and returns the output or Fault object in case of error to client
	 *
	 * @return unknown
	 */
	function serve(){
		LoggerManager::getLogger()->info('Begin: SugarRestJSON->serve');
		$json_data = !empty($_REQUEST['rest_data'])? $GLOBALS['RAW_REQUEST']['rest_data']: '';
		if(empty($_REQUEST['method']) || !method_exists($this->implementation, $_REQUEST['method'])){
			$er = new SoapError();
			$er->set_error('invalid_call');
			$this->fault($er);
		}else{
			$method = $_REQUEST['method'];
			$data = json_decode($json_data);
			if(!is_array($data))$data = array($data);
			$res = call_user_func_array(array( $this->implementation, $method),$data);
			LoggerManager::getLogger()->info('End: SugarRestJSON->serve');
			return $res;
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
		// JSONP support
		if ( isset($_GET["jsoncallback"]) ) {
			echo $_GET["jsoncallback"] . "(";
		}
		echo json_encode($errorObject);
		if ( isset($_GET["jsoncallback"]) ) {
			echo ")";
		}
	} // fn


} // class
