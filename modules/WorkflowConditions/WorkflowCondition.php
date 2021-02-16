<?php
namespace SpiceCRM\modules\WorkflowConditions;

use SpiceCRM\data\SugarBean;

class WorkflowCondition extends SugarBean
{

    var $module_dir = 'WorkflowDefinitions';
    var $object_name = 'WorkflowCondition';
    var $table_name = 'workflowconditions';

    public function conditionMet($bean)
    {

        switch ($this->object_operator) {
            case 'EQ':
                if ($bean->{$this->object_field} == $this->object_value)
                    return true;
                break;
            case 'NE':
                if ($bean->{$this->object_field} != $this->object_value)
                    return true;
                break;
            case 'GT':
                if ($bean->{$this->object_field} > $this->object_value)
                    return true;
                break;
            case 'GE':
                if ($bean->{$this->object_field} >= $this->object_value)
                    return true;
                break;
            case 'LT':
                if ($bean->{$this->object_field} < $this->object_value)
                    return true;
                break;
            case 'LE':
                if ($bean->{$this->object_field} <= $this->object_value)
                    return true;
                break;
        }

        return false;
    }
}
