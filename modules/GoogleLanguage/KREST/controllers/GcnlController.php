<?php
namespace SpiceCRM\modules\GoogleLanguage\KREST\controllers;

use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\modules\GoogleLanguage\GoogleLanguageRestHandler;

class GcnlController{


    public function GcnlAnalyzeSentTime($req, $res, $args){
        $handler = new GoogleLanguageRestHandler();
        $result = $handler->analyzeSentiment($req->getParsedBody());
        return $res->withJson($result);

    }


}