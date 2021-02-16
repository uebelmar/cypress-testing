<?php
namespace SpiceCRM\modules\KnowledgeBooks;

use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\modules\KnowledgeDocuments\KnowledgeDocument;

class KnowledgeBook extends SugarBean {
    public $module_dir  = 'KnowledgeBooks';
    public $object_name = 'KnowledgeBook';
    public $table_name  = 'knowledgebooks';
    public $new_schema  = true;

    public $additional_column_fields = Array();

    public $relationship_fields = Array(
    );


    public function get_summary_text(){
        return $this->name;
    }

    public function bean_implements($interface){
        switch($interface){
            case 'ACL':return true;
        }
        return false;
    }

    /**
     * getDocumentTree
     *
     * Returns data necessary to build a document tree in the UI.
     *
     * @return array
     */
    public function getDocumentTree() {
        $documents = $this->get_linked_beans('knowledgedocuments', 'KnowledgeDocuments');
        $result    = [];

        foreach ($documents as $document) {
            if ($document->status != KnowledgeDocument::STATUS_RELEASED) {
                continue;
            }
            $result[] = [
                'id'              => $document->id,
                'name'            => $document->name,
                'parent_id'       => $document->parent_id,
                'parent_sequence' => $document->parent_sequence,
            ];
        }

        usort($result, function ($a, $b) {
            return $a['parent_sequence'] > $b['parent_sequence'] ? -1 : 1;
        });

        return $result;
    }

    /**
     * getSitemapString
     *
     * Returns XML for sitemap with information about this Book.
     *
     * @return string
     */
    public function getSitemapString() {
        

        $result =
        '<url>
        <loc>' . SpiceConfig::getInstance()->config['knowledgebase']['siteurl'] . '/#/knowledgebook/' . $this->id . '</loc>
        <lastmod>' . $this->date_modified . '</lastmod>
        </url>';

        $documents = $this->get_linked_beans('knowledgedocuments', 'KnowledgeDocuments');
        foreach ($documents as $document) {
            $result .= $document->getSitemapString();
        }

        return $result;
    }
}
