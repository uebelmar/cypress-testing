<?PHP
namespace SpiceCRM\modules\Workflows;

if(file_exists('modules/Workflows/WorkflowPro.php')){
    class Workflow extends WorkflowPro {};
}
else{
    class Workflow extends WorkflowBasic {};
}
