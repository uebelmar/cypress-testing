<?php
/**
 * SpiceCRM backend information
 *
 * $moduleList: array containing a list of modules in the system. The format of the array is to have a numeric index and a value of the modules unique key.
 *
 * $beanList: array that stores a list of all active beans (modules) in the application.
 *
 * $beanFiles: array used to reference the class files for a bean.
 *
 * $modInvisList: removes a module from the navigation tab in the MegaMenu, reporting, and it's subpanels under related modules.
 * To enable a hidden module for reporting, you can use $report_include_modules. To enable a hidden modules subpanels on related modules, you can use $modules_exempt_from_availability_check.
 *
 * $report_include_modules: used in conjunction with $modInvisList. When a module has been hidden with $modInvisList, this will allow for the module to be enabled for reporting.
 *
 * $adminOnlyList: extra level of security for modules that are can be accessed only by administrators through the Admin page. Specifying all will restrict all actions to be admin only.. 
 **/             
//classic settings
$moduleList[] = 'ServiceOrderEfforts';
$beanList['ServiceOrderEfforts'] = 'ServiceOrderEffort';
$beanFiles['ServiceOrderEffort'] = 'modules/ServiceOrderEfforts/ServiceOrderEffort.php';

//possible additional settings
//$modInvisList[] = 'ServiceOrderEfforts';
//$report_include_modules['ServiceOrderEfforts'] = 'ServiceOrderEffort';
//$modules_exempt_from_availability_check['ServiceOrderEfforts']] = 'ServiceOrderEfforts'];
//$adminOnlyList['ServiceOrderEfforts'] = array('all' => 1);
