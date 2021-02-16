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
    'id' => 'kpdfexport', 
    'type' => 'integration', 
    'category' => 'export',
    'icon' => 'modules/KReports/images/pdf.png',
    'displayname' => 'LBL_PDF_EXPORT',
    'integration' => array(
        'include' => 'kpdfexport.php',
        'class' => 'kpdfexport'
    ),
    'includes' => array(
        'view' => 'kpdfexport.js',
        'viewItem' => 'SpiceCRM.KReporter.Viewer.integrationplugins.pdfexport.menuitem',
        'edit' => 'kpdfexportpanel.js',
        'editPanel' => 'SpiceCRM.KReporter.Designer.integrationplugins.pdfexport.panel'
    )
);
