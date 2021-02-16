<?php
namespace SpiceCRM\modules\GoogleLanguage;

use Exception;

class GoogleLanguageRestHandler
{
    public function analyzeSentiment($body) {
        if (!isset($body['content']) || $body['content'] == '') {
            throw new Exception('Content is missing.');
        }

        $document = new GoogleLanguageDocument($body['content']);
        return $document->analyzeSentiment();
    }
}
