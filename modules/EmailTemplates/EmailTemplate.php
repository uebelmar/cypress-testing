<?php
namespace SpiceCRM\modules\EmailTemplates;

use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\SpiceTemplateCompiler\Compiler;
use SpiceCRM\includes\authentication\AuthenticationController;

/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

/*********************************************************************************

 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

// EmailTemplate is used to store email email_template information.
class EmailTemplate extends SugarBean {

	var $table_name = "email_templates";
	var $object_name = "EmailTemplate";
	var $module_dir = "EmailTemplates";


	function __construct() {
		parent::__construct();
	}


    function parse( $bean, $additionalValues = null ){
        global $app_list_strings, $current_language;
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        $app_list_strings = return_app_list_strings_language($this->language);

        $retArray = array(
            'subject' => $this->parsePlainTextField('subject', $bean, $additionalValues ),
            'body' => $this->parseHTMLTextField('body', $bean, $additionalValues ),
            # 'body_html' => '<style>'.$this->getStyle().'</style>'.$this->parseField('body_html', $bean, $additionalValues ),
            'body_html' => $this->parseHTMLTextField('body_html', $bean, $additionalValues ),
        );
        $retArray['subject'] = preg_replace('#\s+#', ' ', $retArray['subject'] ); // multiple white spaces -> one

        return $retArray;
    }

    public function parseHTMLTextField( $field, $parentbean = null, $additionalValues = null )
    {
        $templateCompiler = new Compiler();
        $html = $templateCompiler->compile($this->$field, $parentbean, $this->language, $additionalValues );
        return html_entity_decode($html);
    }

    public function parsePlainTextField($field, $parentbean = null, $additionalValues = null )
    {
        $templateCompiler = new Compiler();
        $text = $templateCompiler->compileblock($this->$field, [ 'bean' => $parentbean ], $this->language, $additionalValues );
        return $text;
    }

    private function getStyle(){
        $style= '';
        if(!empty($this->style)){
            $styleRecord = $this->db->fetchByAssoc($this->db->query("SELECT csscode FROM sysuihtmlstylesheets WHERE id='{$this->style}'"));
            $style = html_entity_decode($styleRecord['csscode'], ENT_QUOTES);
        }
        return $style;
    }

}

