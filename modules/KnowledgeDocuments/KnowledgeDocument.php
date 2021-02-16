<?php
namespace SpiceCRM\modules\KnowledgeDocuments;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\SugarObjects\SpiceConfig;

class KnowledgeDocument extends SugarBean {
    var $parent_id;
    var $name;
    var $breadcrumbs;
    public $module_dir  = 'KnowledgeDocuments';
    public $object_name = 'KnowledgeDocument';
    public $table_name  = 'knowledgedocuments';
    public $new_schema  = true;

    public $additional_column_fields = Array();

    public $relationship_fields = Array(
    );

    const STATUS_DRAFT    = 'Draft';
    const STATUS_RELEASED = 'Released';
    const STATUS_RETIRED  = 'Retired';

    public function get_summary_text(){
        return $this->name;
    }

    public function bean_implements($interface){
        switch($interface){
            case 'ACL':return true;
        }
        return false;
    }

    public function fill_in_additional_detail_fields(){

        parent::fill_in_additional_detail_fields();
        $breadcrumbs = [];
        $this->retrieveBreadcrumbs($breadcrumbs, $this->parent_id);
        $this->breadcrumbs = json_encode($breadcrumbs);
    }

    private function retrieveBreadcrumbs(& $breadcrumbs, $parentId = '') {
        if (!empty($parentId)) {
            $query = "SELECT id, name, parent_id FROM knowledgedocuments WHERE id='$parentId' AND deleted=0";
            $result = $this->db->query($query, true, "Error filling in additional detail fields");
            $breadcrumb = $this->db->fetchByAssoc($result);
            array_unshift($breadcrumbs, $breadcrumb);
            if (!empty($breadcrumb['parent_id']))
                $this->retrieveBreadcrumbs($breadcrumbs, $breadcrumb['parent_id']);
        }
    }

    /**
     * checkPublic
     *
     * Checks if the Document is released and belongs to a public Book.
     *
     * @return bool
     */
    public function checkPublic() {
        $book = BeanFactory::getBean('KnowledgeBooks', $this->knowledgebook_id);
        if (!$book->public || $this->status != self::STATUS_RELEASED) {
            return false;
        }

        return true;
    }

    /**
     * getSitemapString
     *
     * Returns XML for sitemap with information about this Document.
     *
     * @return string
     */
    public function getSitemapString() {
        

        $result =
            '<url>
        <loc>' . SpiceConfig::getInstance()->config['knowledgebase']['siteurl'] . '/#/knowledgedocument/' . $this->id . '</loc>
        <lastmod>' . $this->date_modified . '</lastmod>
        </url>';

        return $result;
    }
}
