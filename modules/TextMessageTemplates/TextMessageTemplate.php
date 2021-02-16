<?php
namespace SpiceCRM\modules\TextMessageTemplates;

use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\SpiceTemplateCompiler\Compiler;
use SpiceCRM\includes\authentication\AuthenticationController;

class TextMessageTemplate extends SugarBean
{
    public $module_dir  = 'TextMessageTemplates';
    public $object_name = 'TextMessageTemplate';
    public $table_name  = 'textmessage_templates';

    public function __construct() {
        parent::__construct();
    }

    public function get_summary_text() {
        return $this->name;
    }

    public function fill_in_additional_list_fields() {
        parent::fill_in_additional_list_fields();
    }

    public function fill_in_additional_detail_fields() {
        parent::fill_in_additional_detail_fields();
    }

    public function bean_implements($interface) {
        switch($interface) {
            case 'ACL':return true;
        }
        return false;
    }

    public function parse($bean, $additionalValues = null) {
        global $app_list_strings, $current_language;
$current_user = AuthenticationController::getInstance()->getCurrentUser();
        $app_list_strings = return_app_list_strings_language($this->language);

        $result = $this->parsePlainTextField('body', $bean, $additionalValues);
        $result = preg_replace('#\s+#', ' ', $result); // multiple white spaces -> one

        return $result;
    }

    public function parsePlainTextField($field, $parentbean = null, $additionalValues = null) {
        $templateCompiler = new Compiler();
        $text = $templateCompiler->compileblock(
            $this->$field,
            ['bean' => $parentbean],
            $this->language,
            $additionalValues
        );
        return $text;
    }
}
