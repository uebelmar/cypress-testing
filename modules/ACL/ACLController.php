<?php

/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

use SpiceCRM\data\BeanFactory;
use SpiceCRM\modules\ACLActions\ACLAction;
use SpiceCRM\includes\authentication\AuthenticationController;
use SpiceCRM\modules\SpiceACL\SpiceACL;

require_once('modules/ACLActions/actiondefs.php');
require_once('modules/ACL/ACLJSController.php');

class ACLController {


    function filterModuleList(&$moduleList, $by_value = true)
    {

        global $aclModuleList;
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        if (is_admin($current_user)) return;
        $actions = ACLAction::getUserActions($current_user->id, false);

        $compList = array();
        if ($by_value) {
            foreach ($moduleList as $key => $value) {
                $compList[$value] = $key;
            }
        } else {
            $compList =& $moduleList;
        }
        foreach ($actions as $action_name => $action) {

            if (!empty($action['module'])) {
                $aclModuleList[$action_name] = $action_name;
                if (isset($compList[$action_name])) {
                    if ($action['module']['access']['aclaccess'] < ACL_ALLOW_ENABLED) {
                        if ($by_value) {
                            unset($moduleList[$compList[$action_name]]);
                        } else {
                            unset($moduleList[$action_name]);
                        }
                    }
                }
            }
        }
        if (isset($compList['Calendar']) &&
            !($this->checkModuleAllowed('Calls', $actions) || $this->checkModuleAllowed('Meetings', $actions) || $this->checkModuleAllowed('Tasks', $actions))
        )
        {
            if ($by_value) {
                unset($moduleList[$compList['Calendar']]);
            } else {
                unset($moduleList['Calendar']);
            }
            if (isset($compList['Activities']) && !$this->checkModuleAllowed('Notes', $actions)) {
                if ($by_value) {
                    unset($moduleList[$compList['Activities']]);
                } else {
                    unset($moduleList['Activities']);
                }
            }
        }

    }

    /**
     * Check to see if the module is available for this user.
     *
     * @param String $module_name
     * @return true if they are allowed.  false otherwise.
     */
    public function checkModuleAllowed($module_name, $actions = array())
    {
        //begin CR1000141
        if(empty($actions))
            return true;
        //end

        if (!empty($actions[$module_name]['module']['access']['aclaccess']) &&
            ACL_ALLOW_ENABLED == $actions[$module_name]['module']['access']['aclaccess']
        ) {
            return true;
        }

        return false;
    }

    public function disabledModuleList($moduleList, $by_value = true, $view = 'list')
    {
        global $aclModuleList;
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        if (is_admin(AuthenticationController::getInstance()->getCurrentUser())) return array();
        $actions = ACLAction::getUserActions($current_user->id, false);
        $disabled = array();
        $compList = array();

        if ($by_value) {
            foreach ($moduleList as $key => $value) {
                $compList[$value] = $key;
            }
        } else {
            $compList =& $moduleList;
		}
        if (isset($moduleList['ProductTemplates'])) {
            $moduleList['Products'] = 'Products';
		}

		foreach($actions as $action_name=>$action){

			if(!empty($action['module'])){
				$aclModuleList[$action_name] = $action_name;
				if(isset($compList[$action_name])){
                    if ($action['module']['access']['aclaccess'] < ACL_ALLOW_ENABLED || $action['module'][$view]['aclaccess'] < 0) {
						if($by_value){
                            $disabled[$compList[$action_name]] = $compList[$action_name];
						}else{
                            $disabled[$action_name] = $action_name;
						}
					}
				}
			}
		}
        if (isset($compList['Calendar']) && !(ACL_ALLOW_ENABLED == $actions['Calls']['module']['access']['aclaccess'] || ACL_ALLOW_ENABLED == $actions['Meetings']['module']['access']['aclaccess'] || ACL_ALLOW_ENABLED == $actions['Tasks']['module']['access']['aclaccess'])) {
			if($by_value){
                $disabled[$compList['Calendar']] = $compList['Calendar'];
			}else{
                $disabled['Calendar'] = 'Calendar';
			}
            if (isset($compList['Activities']) && !(ACL_ALLOW_ENABLED == $actions['Notes']['module']['access']['aclaccess'] || ACL_ALLOW_ENABLED == $actions['Notes']['module']['access']['aclaccess'])) {
				if($by_value){
                    $disabled[$compList['Activities']] = $compList['Activities'];
				}else{
                    $disabled['Activities'] = 'Activities';
				}
			}
		}
        if (isset($disabled['Products'])) {
            $disabled['ProductTemplates'] = 'ProductTemplates';
        }


        return $disabled;

	}

    public function checkAccess($category, $action, $is_owner = false, $type = 'module')
    {


        // for the territorry management we pass int he full object
        if(is_object($category))
            $category = $category->module_dir;

        // check that the module supports ACL ..
        if(!SpiceACL::getInstance()->moduleSupportsACL($category))
            return true;

        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        if (is_admin($current_user)) return true;
        //calendar is a special case since it has 3 modules in it (calls, meetings, tasks)

        if ($category == 'Calendar') {
            return ACLAction::userHasAccess($current_user->id, 'Calls', $action, $type, $is_owner) || ACLAction::userHasAccess($current_user->id, 'Meetings', $action, 'module', $is_owner) || ACLAction::userHasAccess($current_user->id, 'Tasks', $action, 'module', $is_owner);
		}
        if ($category == 'Activities') {
            return ACLAction::userHasAccess($current_user->id, 'Calls', $action, $type, $is_owner) || ACLAction::userHasAccess($current_user->id, 'Meetings', $action, 'module', $is_owner) || ACLAction::userHasAccess($current_user->id, 'Tasks', $action, 'module', $is_owner) || ACLAction::userHasAccess($current_user->id, 'Emails', $action, 'module', $is_owner) || ACLAction::userHasAccess($current_user->id, 'Notes', $action, 'module', $is_owner);
		}
        return ACLAction::userHasAccess($current_user->id, $category, $action, $type, $is_owner);
    }

    /**
     * returns all ACL Actions the user is allowed to do on the bean
     *
     * returns an array with the actionname and true or false
     *
     * @param $bean
     * @return array
     */
    public function getBeanActions($bean)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();

        $aclArray = [];
        $aclActions = ['list', 'detail', 'edit', 'delete', 'export', 'import'];
        foreach ($aclActions as $aclAction) {
            $aclArray[$aclAction] = false;
            if ($bean)
                $aclArray[$aclAction] = $this->checkAccess($aclAction, $bean, $bean->assigned_user_id == $current_user->id);
        }
        return $aclArray;
    }

    /*
     * function to get the field control .. not implemented for standard ACL Controller
     */
    public function getFieldAccess($bean, $view)
    {
        return [];
    }

    public function requireOwner($category, $value, $type = 'module')
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        if (is_admin($current_user)) return false;
        return ACLAction::userNeedsOwnership($current_user->id, $category, $value, $type);
	}

	public function getOwnerWhereClause($bean, $table_name = ''){
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        return "$bean->table_name.assigned_user_id='$current_user->id'";
    }

	function addJavascript($category,$form_name='', $is_owner=false){
		$jscontroller = new ACLJSController($category, $form_name, $is_owner);
		echo $jscontroller->getJavascript();
	}

	public function moduleSupportsACL($module)
    {
		static $checkModules = array();
		global $beanFiles, $beanList;
		if(isset($checkModules[$module])){
			return $checkModules[$module];
		}
		if(!isset($beanList[$module])){
			$checkModules[$module] = false;

		}else{
            $mod = BeanFactory::getBean($module);
			if(!is_subclass_of($mod, 'SugarBean')){
				$checkModules[$module] = false;
			}else{
				$checkModules[$module] = $mod->bean_implements('ACL');
			}
		}
		return $checkModules[$module] ;

	}

	function displayNoAccess($redirect_home = false){
		echo '<script>function set_focus(){}</script><p class="error">' . translate('LBL_NO_ACCESS', 'ACL') . '</p>';
		if($redirect_home)echo translate('LBL_REDIRECT_TO_HOME', 'ACL') . ' <span id="seconds_left">3</span> ' . translate('LBL_SECONDS', 'ACL') . '<script> function redirect_countdown(left){document.getElementById("seconds_left").innerHTML = left; if(left == 0){document.location.href = "index.php";}else{left--; setTimeout("redirect_countdown("+ left+")", 1000)}};setTimeout("redirect_countdown(3)", 1000)</script>';
	}

    /**
     * generates an FTS query object
     *
     * @param $module
     *
     * @return array
     */
	function getFTSQuery($module){
	    $current_user = AuthenticationController::getInstance()->getCurrentUser();

        $thisFilter = [];
        if ($this->requireOwner($module, 'list')) {
            $thisFilter['should'][] = array(
                'term' => array(
                    'assigned_user_id' => $current_user->id
                )
            );
        }

        return $thisFilter;

    }

    function getModuleAccess($module){
        $aclArray = [];
        $aclActions = ['list', 'listrelated', 'view', 'delete', 'edit', 'create', 'export', 'import'];
        foreach ($aclActions as $aclAction) {
            // $aclArray[$aclAction] = $seed->ACLAccess($aclAction);
            $aclArray[$aclAction] = $this->checkAccess($module, $aclAction, true);
        }
        return $aclArray;
    }

}
