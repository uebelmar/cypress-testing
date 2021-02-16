<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

namespace SpiceCRM\modules\Documents\KREST\controllers;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\ErrorHandlers\NotFoundException;
use SpiceCRM\includes\SpiceAttachments\SpiceAttachments;

/**
 * Class DocumentsKRESTController
 *
 * @package SpiceCRM\modules\Documents\KREST\controllers
 */
class DocumentsKRESTController
{
    /**
     * createas a new revision from a base64 string
     *
     * @param $req
     * @param $res
     * @param $args
     */
    public function revisionFromBase64($req, $res, $args){
        $document = BeanFactory::getBean('Documents', $args['id']);
        if(!$document){
            throw new NotFoundException('Document not found');
        }

        $body = $req->getParsedBody();

        $documentRevision = BeanFactory::getBean('DocumentRevisions');
        $documentRevision->id = create_guid();
        $documentRevision->new_with_id = true;

        // generate the attachment
        $attachment = SpiceAttachments::saveAttachmentHashFiles('DocumentRevisions', $documentRevision->id, ['filename' => $body['file_name'],  'file' => $body['file'], 'filemimetype' => $body['file_mime_type']]);

        $documentRevision->file_name = $body['file_name'];
        $documentRevision->file_md5 = $attachment['filemd5'];
        $documentRevision->file_mime_type = $body['file_mime_type'];

        $documentRevision->document_id = $document->id;
        $documentRevision->documentrevisionstatus = $body['documentrevisionstatus'];
        $documentRevision->save();



    }
}
