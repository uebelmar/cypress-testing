<?php
namespace SpiceCRM\modules\KnowledgeBooks\KREST\controllers;

use SpiceCRM\modules\KnowledgeDocumentAccessLogs\KnowledgeDocumentAccessLog;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\ErrorHandlers\Exception;

class KnowledgeBooksKRESTController
{

    /**
     * getBookList
     *
     * Returns a list of the public KnowledgeBooks
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function getBookList($req, $res, $args) {
        $beanList = $this->getPublicBooks();
        $bookList = [];

        foreach ($beanList as $book) {
            $bookList[] = [
                'id'   => $book->id,
                'name' => $book->name,
                'html' => $book->html,
            ];
        }

        return $res->withJson($bookList);
    }

    /**
     * getBook
     *
     * Returns a public KnowledgeBook with its details and documents as an array.
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     * @throws Exception
     */
    public function getBook($req, $res, $args) {
        $book = BeanFactory::getBean('KnowledgeBooks', $args['bookId']);

        if (!$book->public) {
            throw new Exception('Access denied', 403);
        }

        return $res->withJson($book->getDocumentTree());
    }

    /**
     * getDocument
     *
     * Returns a KnowledgeDocument with its contents.
     * Check if the Document is part of a public Book, and if it's published.
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     * @throws Exception
     */
    public function getDocumentContent($req, $res, $args) {
        $document = BeanFactory::getBean('KnowledgeDocuments', $args['documentId']);

        if (!$document) {
            throw new Exception('Not found', 404);
        }

        if ($document->checkPublic() == false) {
            throw new Exception('Access denied', 403);
        }

        KnowledgeDocumentAccessLog::incrementCounter($document->id);

        return $res->withJson([
            'bookId'     => $document->knowledgebook_id,
            'content'    => html_entity_decode($document->description),
            'documentId' => $document->id,
        ]);
    }

    /**
     * getSitemap
     *
     * Returns a sitemap for the Knowledge Books and Documents
     *
     * @return string
     */
    public function getSitemap() {
        $books = $this->getPublicBooks();
        $result = '<?xml version="1.0" encoding="UTF-8"?>
                    <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
                    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                    xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
                    http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';

        foreach ($books as $book) {
            $result .= $book->getSitemapString();
        }

        $result .= '</urlset>';

        return $result;
    }

    /**
     * getPublicBooks
     *
     * Returns all public books.
     *
     * @return array
     */
    private function getPublicBooks() {
        $bean     = BeanFactory::getBean('KnowledgeBooks');
        $beanList = $bean->get_full_list("", "knowledgebooks.public=1");

        return $beanList;
    }
}
