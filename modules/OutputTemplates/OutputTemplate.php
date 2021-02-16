<?php
namespace SpiceCRM\modules\OutputTemplates;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\SpiceAttachments\SpiceAttachments;
use SpiceCRM\includes\SpiceTemplateCompiler\Compiler;
use SpiceCRM\includes\SugarObjects\SpiceConfig;

/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

/*********************************************************************************
 * Description:  represents a template to output something to a .pdf or like that...
 * Contributor(s): Sebastian Franz
 ********************************************************************************/

class OutputTemplate extends SugarBean
{
    var $table_name = "outputtemplates";
    var $object_name = "OutputTemplate";
    var $module_dir = "OutputTemplates";
    var $new_schema = true;
    // fields which holds options to create pdfs
    public static $PDF_OPTION_FIELDS = [
        'page_size',
        'page_orientation',
        'margin_left',
        'margin_top',
        'margin_right',
        'margin_bottom'
    ];
    private $pdf_handler;

    function get_summary_text()
    {
        return "$this->name";
    }

    function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }


    public function translateBody($bean = null, $bodyOnly = false)
    {
        if(!$bean)
        {
            if(!$this->bean)
                $this->retrieveBean();

            $bean = $this->bean;
        }
        if(!$bean)
            throw new Exception("No Bean found, translation aborted!");

        $templateCompiler = new Compiler();
        if ($bodyOnly) {
            $html = $templateCompiler->compile(html_entity_decode( $this->body), $bean, $this->language );
        } else {
            $html = '<style>' . $this->getStyle() . '</style>' . $templateCompiler->compile('<body><header>'
                    .html_entity_decode( $this->header ).'</header><footer>'.html_entity_decode( $this->footer ).'</footer><main>'.html_entity_decode( $this->body ).'</main></body>', $bean, $this->language );
        }

        return $html;
    }

    public function __toString()
    {
        return $this->translateBody();
    }

    private function setPDFHandler(){
        $class = @SpiceConfig::getInstance()->config['outputtemplates']['pdf_handler_class'];
        if(!$class) $class = '\SpiceCRM\modules\OutputTemplates\handlers\pdf\DomPdfHandler';
        $this->pdf_handler = new $class($this);
    }

    public function download()
    {
        $this->setPDFHandler();
        return $this->pdf_handler->toDownload();
    }

    private function saveAsTmpFile($filename = null)
    {
        $this->setPDFHandler();
        return $this->pdf_handler->toTempFile($filename);
    }

    public function getFileName()
    {
        return "{$this->module_name}_{$this->name}.pdf";
    }

    public function getPdfContent()
    {
        $this->setPDFHandler();
        return $this->pdf_handler->__toString();
    }

    public function convertToSpiceAttatchment()
    {
        $file = $this->saveAsTmpFile();
        return SpiceAttachments::saveAttachmentLocalFile($this->module_name, $this->bean_id, $file);
    }

    private function retrieveBean()
    {
        if ($this->bean_id) {
            $this->bean = BeanFactory::getBean($this->module_name, $this->bean_id);
        }

        return $this->bean;
    }

    public function getStyle()
    {
        $style = '';
        if (!empty($this->stylesheet_id)) {
            $styleRecord = $this->db->fetchByAssoc($this->db->query("SELECT csscode FROM sysuihtmlstylesheets WHERE id='{$this->stylesheet_id}'"));
            $style = html_entity_decode($styleRecord['csscode'], ENT_QUOTES);
        }
        return str_replace(["\n", "\t"], "", $style);
    }

}

