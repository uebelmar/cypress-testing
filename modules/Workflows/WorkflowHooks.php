<?php
/* * *******************************************************************************
 * This file is part of Workflow. Workflow is an enhancement developed
 * by AAC s.r.o. All rights are (c) 2014 by AAC s.r.o
 *
 * This Version of the Workflow is licensed software and may only be used in
 * alignment with the License Agreement received with this Software.
 * This Software is copyrighted and may not be further distributed without
 * witten consent of AAC s.r.o
 *
 * You can contact us at office@all-about-crm.com
 * ****************************************************************************** */

namespace SpiceCRM\modules\Workflows;

use SpiceCRM\data\BeanFactory;

class WorkflowHooks{

    public function WorkflowHandler(&$bean, $event, $arguments){
        switch($event){
            case 'after_save':
                $workflow = BeanFactory::getBean('Workflows');
                if($workflow) {
                    $workflow->WorkflowHandler($bean);
                }
                break;
            case 'before_delete':
                $workflow = BeanFactory::getBean('Workflows');
                if($workflow) {
                    $workflow->WorkflowMarkDelted($bean->id);
                }
                break;
        }

    }
}
