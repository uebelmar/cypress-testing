<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\Logger\LoggerManager;
use SpiceCRM\modules\ACLActions\ACLAction;

global $current_user,$beanList, $beanFiles, $mod_strings;

$installed_classes = array();
$ACLbeanList=$beanList;

if(is_admin($current_user)){
    foreach($ACLbeanList as $module=>$class){

        if(empty($installed_classes[$class]) && isset($beanFiles[$class]) && file_exists($beanFiles[$class])){
            if($class == 'Tracker'){
            } else {
                $mod = BeanFactory::getBean($module);
                LoggerManager::getLogger()->debug("DOING: $class");
                if($mod->bean_implements('ACL') && empty($mod->acl_display_only)){
                    // BUG 10339: do not display messages for upgrade wizard
                    if(!isset($_REQUEST['upgradeWizard'])){
                        echo translate('LBL_ADDING','ACL','') . $mod->module_dir . '<br>';
                    }
                    if(!empty($mod->acltype)){
                        ACLAction::addActions($mod->getACLCategory(), $mod->acltype);
                    }else{
                        ACLAction::addActions($mod->getACLCategory());
                    }

                    $installed_classes[$class] = true;
                }
            }
        }
    }


}
