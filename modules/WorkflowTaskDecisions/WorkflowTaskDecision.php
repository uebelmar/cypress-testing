<?PHP
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
namespace SpiceCRM\modules\WorkflowTaskDecisions;

use SpiceCRM\data\SugarBean;

class WorkflowTaskDecision extends SugarBean
{
    var $module_dir = 'WorkflowTaskDecisions';
    var $object_name = 'WorkflowTaskDecision';
    var $table_name = 'workflowtaskdecisions';

    function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }
}
