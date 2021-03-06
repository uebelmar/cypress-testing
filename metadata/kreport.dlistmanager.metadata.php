<?php

/* * *******************************************************************************
 * This file is part of KReporter. KReporter is an enhancement developed
 * by Christian Knoll. All rights are (c) 2012 by Christian Knoll
 *
 * This Version of the KReporter is licensed software and may only be used in
 * alignment with the License Agreement received with this Software.
 * This Software is copyrighted and may not be further distributed without
 * witten consent of Christian Knoll
 *
 * You can contact us at info@kreporter.org
 * ****************************************************************************** */

$dictionary['KReportDLists'] = array(
    'table' => 'kreportdlists',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'id',
        ),
        'name' => array(
            'name' => 'name',
            'type' => 'varchar'
        ),
        'description' => array(
            'name' => 'description',
            'type' => 'text'
        ),  
        'dlistdata' => array(
            'name' => 'dlistdata',
            'type' => 'longtext'
        ), 
        'deleted' => array(
            'name' => 'deleted',
            'type' => 'bool',
	    'default' => 0,
        ),
    ),
    'indices' => array(
        array('name' => 'kreportdlistspk', 'type' => 'primary', 'fields' => array('id')),
    ),
);

