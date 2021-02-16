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

$dictionary['kreportschedulers'] = array(
	'table' => 'kreportschedulers',
        'fields' => array (
           'id' => array(
                  'name' => 'id',
                  'type' => 'id',
           ),
           'report_id' => array(
	          'name' => 'report_id',
	          'type' => 'id',
	   ),
	   'job_id' => array(
	          'name' => 'job_id',
	          'type' => 'id',
	   ),
	   'timestamp' => array(
	   	  'name' => 'timestamp',
	          'type' => 'datetime'
	   ),
            'status' => array(
                'name' => 'status', 
                'type' => 'char', 
                'len' => 1
            )
   	),
   	'indices' => array (
        array('name' => 'kreportschedulerspk', 'type' => 'primary', 'fields' => array('id'))
	)
);
