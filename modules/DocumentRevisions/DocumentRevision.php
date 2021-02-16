<?php
namespace SpiceCRM\modules\DocumentRevisions;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/


class DocumentRevision extends SugarBean {


	var $table_name = "document_revisions";	
	var $object_name = "DocumentRevision";
	var $module_dir = 'DocumentRevisions';



	function __construct() {
		parent::__construct();
		$this->disable_row_level_security =true; //no direct access to this module.
	}

	function save($check_notify = false, $fts_index_bean = true){
        global $timedate;

	    // if this is new issue a revision number and set the status to created
        if(empty($this->revision)){
            $this->revision = $this->getNextDocumentRevision();
        }

        if($this->documentrevisionstatus == 'r' && $this->documentrevisionstatus != $this->fetched_row['documentrevisionstatus']){
            $this->archiveAllRevisions();

            // load and update the document
            $document = BeanFactory::getBean('Documents', $this->document_id);
            $document->revision = $this->revision;
            $document->revision_date = $timedate->nowDb();
            $document->file_name = $this->file_name;
            $document->file_md5 = $this->file_md5;
            $document->file_mime_type = $this->file_mime_type;
            $document->save();
        }

        return parent::save($check_notify, $fts_index_bean);
	}

	function get_summary_text()
	{
		return $this->document_name . ' / ' . $this->revision;
	}

    /**
     * determine the next document revision
     *
     * @param $doc_revision_id
     * @return null
     */
	function getNextDocumentRevision(){

	    $res = $this->db->fetchByAssoc($this->db->query("SELECT max(revision) maxrevision FROM $this->table_name WHERE document_id ='$this->document_id' AND deleted = 0"));
	    if($res){
	        return (int)$res['maxrevision'] + 1;
        }

	    return 0;
	}

    /**
     * find all revisions that have a status released and set them to active
     */
	private function archiveAllRevisions(){
	    $active = $this->get_full_list("", "{$this->table_name}.documentrevisionstatus = 'r'");
	    foreach($active as $activeDocument){
	        $activeDocument->documentrevisionstatus = 'a';
	        $activeDocument->save();
        }
    }

}

