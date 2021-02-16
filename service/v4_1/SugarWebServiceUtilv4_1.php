<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\Logger\LoggerManager;
use SpiceCRM\includes\LogicHook\LogicHook;
use SpiceCRM\includes\authentication\AuthenticationController;

require_once('service/v4/SugarWebServiceUtilv4.php');

class SugarWebServiceUtilv4_1 extends SugarWebServiceUtilv4
{
    /**
   	 * Validate the provided session information is correct and current.  Load the session.
   	 *
   	 * @param String $session_id -- The session ID that was returned by a call to login.
   	 * @return true -- If the session is valid and loaded.
   	 * @return false -- if the session is not valid.
   	 */
   	function validate_authenticated($session_id)
    {
   		LoggerManager::getLogger()->info('Begin: SoapHelperWebServices->validate_authenticated');
   		if(!empty($session_id)){

   			// only initialize session once in case this method is called multiple times
   			if(!session_id()) {
   			   session_id($session_id);
   			   session_start();
   			}

   			if(!empty($_SESSION['is_valid_session']) && $this->is_valid_ip_address('ip_address') && $_SESSION['type'] == 'user'){

                $authController= AuthenticationController::getInstance();
   				require_once('modules/Users/User.php');
   				$authController->setCurrentUser(BeanFactory::getBean('Users'));
   				$current_user = $authController->getCurrentUser();
   				$current_user->retrieve($_SESSION['user_id']);
   				$this->login_success();
   				LoggerManager::getLogger()->info('Begin: SoapHelperWebServices->validate_authenticated - passed');
   				LoggerManager::getLogger()->info('End: SoapHelperWebServices->validate_authenticated');
   				return true;
   			}

   			LoggerManager::getLogger()->debug("calling destroy");
   			session_destroy();
   		}
   		LogicHook::initialize();
   		$GLOBALS['logic_hook']->call_custom_logic('Users', 'login_failed');
   		LoggerManager::getLogger()->info('End: SoapHelperWebServices->validate_authenticated - validation failed');
   		return false;
   	}


    function check_modules_access($user, $module_name, $action='write'){
        if(!isset($_SESSION['avail_modules'])){
            $_SESSION['avail_modules'] = get_user_module_list($user);
        }
        if(isset($_SESSION['avail_modules'][$module_name])){
            if($action == 'write' && $_SESSION['avail_modules'][$module_name] == 'read_only'){
                if(is_admin($user))return true;
                return false;
            }elseif($action == 'write' && strcmp(strtolower($module_name), 'users') == 0 && !$user->isAdminForModule($module_name)){
                //rrs bug: 46000 - If the client is trying to write to the Users module and is not an admin then we need to stop them
                return false;
            }
            return true;
        }
        return false;

    }

    /**
     * getRelationshipResults
     * Returns the
     *
     * @param Mixed $bean The SugarBean instance to retrieve relationships from
     * @param String $link_field_name The name of the relationship entry to fetch relationships for
     * @param Array $link_module_fields Array of fields of relationship entries to return
     * @param string $optional_where String containing an optional WHERE select clause
     * @param string $order_by String containing field to order results by
     * @param Number $offset -- where to start in the return (defaults to 0)
     * @param Number $limit -- number of results to return (defaults to all)
     * @return array|bool Returns an Array of relationship results; false if relationship could not be retrieved
     */
    function getRelationshipResults($bean, $link_field_name, $link_module_fields, $optional_where = '', $order_by = '', $offset = 0, $limit = '') {
        LoggerManager::getLogger()->info('Begin: SoapHelperWebServices->getRelationshipResults');

		global $beanList, $beanFiles;
$current_user = AuthenticationController::getInstance()->getCurrentUser();
		global $timedate;

		$bean->load_relationship($link_field_name);

		if (isset($bean->$link_field_name)) {
			//First get all the related beans
            $params = array();
            $params['offset'] = $offset;
            $params['limit'] = $limit;

            if (!empty($optional_where))
            {
                $params['where'] = $optional_where;
            }

            $related_beans = $bean->$link_field_name->getBeans($params);
            //Create a list of field/value rows based on $link_module_fields
			$list = array();
            $filterFields = array();
            if (!empty($order_by) && !empty($related_beans))
            {
                $related_beans = order_beans($related_beans, $order_by);
            }
            foreach($related_beans as $id => $bean)
            {
                if (empty($filterFields) && !empty($link_module_fields))
                {
                    $filterFields = $this->filter_fields($bean, $link_module_fields);
                }
                $row = array();
                foreach ($filterFields as $field) {
                    if (isset($bean->$field))
                    {
                        $row[$field] = $bean->$field;
                    }
                    else
                    {
                        $row[$field] = "";
                    }
                }
                //Users can't see other user's hashes
                if(is_a($bean, 'User') && $current_user->id != $bean->id && isset($row['user_hash'])) {
                    $row['user_hash'] = "";
                }
                $row = clean_sensitive_data($bean->field_defs, $row);
                $list[] = $row;
            }
            LoggerManager::getLogger()->info('End: SoapHelperWebServices->getRelationshipResults');
            return array('rows' => $list, 'fields_set_on_rows' => $filterFields);
		} else {
			LoggerManager::getLogger()->info('End: SoapHelperWebServices->getRelationshipResults - ' . $link_field_name . ' relationship does not exists');
			return false;
		} // else

	} // fn
}
