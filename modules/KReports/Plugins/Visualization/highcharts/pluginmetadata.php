<?php

$pluginmetadata = array(
    'id' => 'highcharts',
    'displayname' => 'LBL_HIGHCHARTS',
    'type' => 'visualization',
    'visualization' => array(
        'include' => 'KHighCharts.php',
        'class' => 'KHighChart'
    ),
    'pluginpanel' => 'SpiceCRM.KReporter.Designer.visualizationplugins.highchartspanel',
    'viewpanel' => 'SpiceCRM.KReporter.Viewer.visualizationplugins.highchartsviz',
    'includes' => array(
        'edit' => 'highchartspanel.js',
        'view' => 'highchartsviz.js'
    )
);
