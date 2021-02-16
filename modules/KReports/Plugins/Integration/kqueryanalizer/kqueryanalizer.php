<?php
/* * *******************************************************************************
* This file is part of KReporter. KReporter is an enhancement developed
* by aac services k.s.. All rights are (c) 2016 by aac services k.s.
*
* This Version of the KReporter is licensed software and may only be used in
* alignment with the License Agreement received with this Software.
* This Software is copyrighted and may not be further distributed without
* witten consent of aac services k.s.
*
* You can contact us at info@kreporter.org
******************************************************************************* */


use SpiceCRM\includes\SugarObjects\SpiceConfig;

require_once('modules/KReports/Plugins/prototypes/kreportintegrationplugin.php');

class kqueryanalizer extends kreportintegrationplugin {

    public function __construct() {
        $this->pluginName = 'Query Analizer';
    }
    
    public function checkAccess($thisReport){
        return true;
    }

    public function getMenuItem() {
          return array(
            'jsFile' => 'custom/modules/KReports/Plugins/Integration/kqueryanalizer/kqueryanalizer' . (SpiceConfig::getInstance()->config['KReports']['debug'] ? '_debug' : '') . '.js',
            'menuItem' => array(
                'icon' => $this->wrapText('modules/KReports/images/sql.png'),
                'text' => $this->wrapText($this->pluginName),
                'handler' => $this->wrapFunction('K.kreports.kqueryanalizer.window.show')
                ));
    }

}


