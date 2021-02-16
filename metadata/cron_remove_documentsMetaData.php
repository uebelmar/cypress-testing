<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/


$dictionary['cron_remove_documents'] = array (
    'table' => 'cron_remove_documents',
    'fields' => array(
        array(
            'name' => 'id',
            'type' => 'varchar',
            'len' => '36'
        ),
        array(
            'name' => 'bean_id',
            'type' => 'varchar',
            'len' => '36'
        ),
        array(
            'name' => 'module',
            'type' => 'varchar',
            'len' => '25'
        ),
        array(
            'name' =>'date_modified',
            'type' => 'datetime'
        )
    ),
    'indices' => array(
        array(
            'name' => 'cron_remove_documentspk',
            'type' =>'primary',
            'fields'=>array('id')
        ),
        array(
            'name' => 'idx_cron_remove_document_bean_id',
            'type' => 'index',
            'fields' => array('bean_id')
        ),
        array(
            'name' => 'idx_cron_remove_document_stamp',
            'type' => 'index',
            'fields' => array('date_modified')
        )
    )
);
