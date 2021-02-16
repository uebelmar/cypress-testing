<?php
 if(!defined('sugarEntry'))define('sugarEntry', true);
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/


/**
 * This is an abstract class for all the web services.
 * All type of web services should provide proper implementation of all the abstract methods
 * @api
 */
abstract class SugarWebService{
	protected $server = null;
	protected $excludeFunctions = array();
	abstract function register($excludeFunctions = array());
	abstract function registerImplClass($class);
	abstract function getRegisteredImplClass();
	abstract function registerClass($class);
	abstract function getRegisteredClass();
	abstract function serve();
	abstract function error($errorObject);
}
