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

$dictionary['KReportSnapshots'] = array(
    'table' => 'kreportsnapshots',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'type' => 'id',
        ),
        'report_id' => array(
            'name' => 'report_id',
            'type' => 'id',
        ),
        'snapshotdate' => array(
            'name' => 'snapshotdate',
            'type' => 'datetime',
        ),
        'snapshotquery' => array(
            'name' => 'snapshotquery',
            'type' => 'text',
        ),
        'data' => array(
            'name' => 'data',
            'type' => 'longblob'
        ),
    ),
    'indices' => array(
        array('name' => 'kreportsnapshotspk', 'type' => 'primary', 'fields' => array('id'))
    ),
);

$dictionary['KReportSnapshotsData'] = array(
    'table' => 'kreportsnapshotsdata',
    'fields' => array(
        /* 	   'id' => array(
          'name' => 'id',
          'type' => 'id',
          ), */
        'snapshot_id' => array(
            'name' => 'snapshot_id',
            'type' => 'id',
            'required' => true,
            'isnull' => false,
        ),
        'record_id' => array(
            'name' => 'record_id',
            'type' => 'double',
            // bug #525 fixed MSSQL erro in installation
            'required' => true,
            'isnull' => false,
        ),
        'data' => array(
            'name' => 'data',
            'type' => 'blob',
        ),
    ),
    'indices' => array(
        array('name' => 'kreportsnapshotsdatapk', 'type' => 'primary', 'fields' => array('snapshot_id', 'record_id'))
    ),
);
