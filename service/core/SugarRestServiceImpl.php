<?php
if(!defined('sugarEntry'))define('sugarEntry', true);
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/


/**
 * This class is an implemenatation class for all the rest services
 */
require_once('service/core/SugarWebServiceImpl.php');
class SugarRestServiceImpl extends SugarWebServiceImpl {
	
	function md5($string){
		return md5($string);
	}
}
require_once('service/core/SugarRestUtils.php');
SugarRestServiceImpl::$helperObject = new SugarRestUtils();
