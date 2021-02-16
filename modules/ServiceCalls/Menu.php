<?php 
 

global $mod_strings, $app_strings;

use SpiceCRM\modules\SpiceACL\SpiceACL;

$module_menu = Array();
if(SpiceACL::getInstance()->checkAccess('ServiceCalls','edit',true)){
    $module_menu[]=	Array("index.php?module=ServiceCalls&action=EditView&return_module=ServiceCalls&return_action=DetailView", $mod_strings['LNK_NEW_SERVICECALL'],"CreateServiceCalls");
}
if(SpiceACL::getInstance()->checkAccess('ServiceCalls','list',true)){
    $module_menu[]=	Array("index.php?module=ServiceCalls&action=index&return_module=ServiceCalls&return_action=DetailView", $mod_strings['LNK_SERVICECALL_LIST'],"ServiceCalls");
}
if(SpiceACL::getInstance()->checkAccess('ServiceCalls','import',true)){
    $module_menu[]=  Array("index.php?module=Import&action=Step1&import_module=ServiceCalls&return_module=ServiceCalls&return_action=index", $mod_strings['LNK_IMPORT_SERVICECALLS'],"Import");
}
