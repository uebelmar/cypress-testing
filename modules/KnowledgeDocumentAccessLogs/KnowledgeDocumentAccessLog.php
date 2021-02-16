<?php
namespace SpiceCRM\modules\KnowledgeDocumentAccessLogs;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\database\DBManagerFactory;

class KnowledgeDocumentAccessLog extends SugarBean
{
    public $module_dir  = 'KnowledgeDocumentAccessLogs';
    public $table_name  = "knowledgedocumentaccesslogs";
    public $object_name = "KnowledgeDocumentAccessLog";

    public function bean_implements($interface) {
        switch($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }

    public function get_summary_text()
    {
        return $this->name;
    }

    /**
     * incrementCounter
     *
     * Increments the access log counter for the given Document and the current date.
     *
     * @param $document_id
     */
    public static function incrementCounter($document_id) {
        $log = self::getCurrentLog($document_id);
        ++$log->counter;
        $log->save();
    }

    /**
     * getCurrentLog
     *
     * Returns the access log for the given Document and the current date or creates a new one.
     *
     * @param $document_id
     * @return SugarBean
     */
    public static function getCurrentLog($document_id) {
        $db = DBManagerFactory::getInstance();
        $logId = null;
        $sql = 'SELECT id FROM knowledgedocumentaccesslogs' .
                ' WHERE knowledgedocument_id = "' . $document_id . '"' .
                ' AND stat_date = "' . date('Y-m-d') . '"';

        $result = $db->fetchOne($sql);
        if ($result) {
            $logId = $result['id'];
        }

        $log = BeanFactory::getBean('KnowledgeDocumentAccessLogs', $logId);

        if (!$log->stat_date) {
            $log->stat_date = date('Y-m-d');
            $log->knowledgedocument_id = $document_id;
        }

        return $log;
    }
}
