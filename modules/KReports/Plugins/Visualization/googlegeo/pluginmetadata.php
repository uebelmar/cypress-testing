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





$pluginmetadata = array(
    'id' => 'googlegeo',
    'displayname' => 'LBL_GOOGLEGEO',
    'type' => 'visualization',
    'visualization' => array(
        'include' => 'kGoogleGeo.php',
        'class' => 'kGoogleGeo'
    ),
    'pluginpanel' => 'SpiceCRM.KReporter.Designer.visualizationplugins.googlegeopanel',
    'viewpanel' => 'SpiceCRM.KReporter.Designer.visualizationplugins.googlegeoviz',
    'includes' => array(
        'edit' => 'googlegeopanel.js',
        'view' => 'googlegeoviz.js'
    )
);
