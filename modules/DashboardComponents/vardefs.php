<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\SugarObjects\VardefManager;

$dictionary['DashboardComponent'] = array(
    'table' => 'dashboardcomponents',
    'fields' =>
        array(
            'dashboard_id' => array(
                'name' => 'dashboard_id',
                'type' => 'id'
            ),
            'dashlet_id' => array(
                'name' => 'dashlet_id',
                'type' => 'varchar',
                'len' => 36
            ),
            'component' => array(
                'name' => 'component',
                'type' => 'varchar',
                'len' => 100
            ),
            'componentconfig' => array(
                'name' => 'componentconfig',
                'type' => 'text'
            ),
            'position' => array(
                'name' => 'position',
                'type' => 'text'
            )
        )
);


VardefManager::createVardef('DashboardComponents', 'DashboardComponent', array('default'));

