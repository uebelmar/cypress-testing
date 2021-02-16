<?php
namespace SpiceCRM\modules\KnowledgeDocuments\KREST\controllers;

use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\ErrorHandlers\ForbiddenException;
use SpiceCRM\modules\SpiceACL\SpiceACL;

class KnowledgeDocumentsKRESTController
{
    /**
     * @param $req
     * @param $res
     * @param $args
     * @return bool
     * @throws ForbiddenException
     */

    public function releaseAllChildren($req, $res, $args) {
        if (!SpiceACL::getInstance()->checkAccess('KnowledgeDocuments', 'edit', true))
            throw (new ForbiddenException("Forbidden to edit in module KnowledgeDocuments."))->setErrorCode('noModuleEdit');

        $db = DBManagerFactory::getInstance();
        $docId = $args['id'];
        $ids = [];
        $this->getChildrenIds($db, $ids, $docId);
        $idsString = "'". $docId . "','" . implode ( "', '", $ids) . "'";
        $query = "UPDATE knowledgedocuments SET status = 'Released' WHERE id in ($idsString)";
        $query = $db->query($query);
        return $res->withJson(['released' => $query, 'ids' => $ids]);
    }

    private function getChildrenIds($db, &$ids, $parentId) {
        $query = "SELECT id, parent_id FROM knowledgedocuments WHERE parent_id = '$parentId' AND deleted = 0";
        $query = $db->query($query);

        while ($row = $db->fetchByAssoc($query)) {
            $ids[$row['id']] = $row['id'];
            $this->getChildrenIds($db, $ids, $row['id']);
        }
    }
    /**
     * @param $req
     * @param $res
     * @param $args
     * @return bool
     * @throws ForbiddenException
     */

    public function modifySortSequence($req, $res, $args) {
        if (!SpiceACL::getInstance()->checkAccess('KnowledgeDocuments', 'edit', true))
            throw (new ForbiddenException("Forbidden to edit in module KnowledgeDocuments."))->setErrorCode('noModuleEdit');

        $db = DBManagerFactory::getInstance();
        $listItems = $req->getParsedBody();

        foreach ($listItems as $listItem) {
            $query = "UPDATE knowledgedocuments SET parent_sequence = ". $listItem['index'] ." WHERE id = '". $listItem['id'] ."'";
            $query = $db->query($query);
        }

        return $res->withJson(['status' => $query]);
    }

}
