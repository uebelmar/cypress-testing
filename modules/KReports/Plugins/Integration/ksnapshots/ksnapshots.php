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

class ksnapshot extends kreportintegrationplugin {

   public function __construct() {
      $this->pluginName = 'Snapshots';
   }

   public function getMenuItem() {
      
      return array(
          'jsFile' => 'modules/KReports/Plugins/Integration/ksnapshots/ksnapshot' . (SpiceConfig::getInstance()->config['KReports']['debug'] ? '_debug' : '') . '.js',
          'menuItem' => array(
              'icon' => $this->wrapText('modules/KReports/images/snapshot.png'),
              'text' => $this->wrapText($this->pluginName),
              'menu' => array(
                  // 'K.kreports.ksnapshot.snapshotCombo',
                  array(
                      'text' => $this->wrapText('take Snapshot'),
                      'icon' => $this->wrapText('modules/KReports/images/snapshot.png'),
                      'handler' => $this->wrapFunction('K.kreports.ksnapshot.takeSnapshot')
                  ),
                  'K.kreports.ksnapshot.snapshotCombo'
              )
      ));
   }

}