<?php
namespace SpiceCRM\modules\GoogleLanguage;

use Exception;
use SpiceCRM\includes\SugarObjects\SpiceConfig;

/**
 * Class GoogleLanguageDocument
 *
 * Connects to the Google Cloud Natural Language API.
 *
 * @package SpiceCRM\modules\GoogleLanguage
 */
class GoogleLanguageDocument
{
    private $content;
    private $language;
    private $type = 'HTML';
    private $gcsContentUri;

    private $apiKey;
    private $encoding = 'UTF8';

    public const API_URL = 'https://language.googleapis.com/v1beta2/';

    public const CONTENT_TYPES = [
        'TYPE_UNSPECIFIED' => 'TYPE_UNSPECIFIED',
        'PLAIN_TEXT'       => 'PLAIN_TEXT',
        'HTML'             => 'HTML',
    ];

    /**
     * Check here for updates
     * https://cloud.google.com/natural-language/docs/languages
     */
    public const SUPPORTED_LANGUAGES = [
        'CHINESE_SIMPLIFIED'  => 'zh',
        'CHINESE_TRADITIONAL' => 'zh-Hant',
        'ENGLISH'             => 'en',
        'FRENCH'              => 'fr',
        'GERMAN'              => 'de',
        'ITALIAN'             => 'it',
        'JAPANESE'            => 'ja',
        'KOREAN'              => 'ko',
        'PORTUGUESE'          => 'pt',
        'SPANISH'             => 'es',
    ];

    public const SUPPORTED_ENCODING = [
        'NONE'  => 'NONE',
        'UTF8'  => 'UTF8',
        'UTF16' => 'UTF16',
        'UTF32' => 'UTF32',
    ];

    /**
     * GoogleLanguageDocument constructor.
     * @param $content
     */
    public function __construct($content) {

        $this->apiKey        = SpiceConfig::getInstance()->config['googlelanguage']['apikey'];
        $this->content       = $content;
    }

    /**
     * analyzeSentiment
     *
     * Returns the sentiment analysis of the provided text.
     *
     * @return mixed
     * @throws Exception
     */
    public function analyzeSentiment() {
        $apiUrl = self::API_URL . 'documents:analyzeSentiment?key=' . $this->apiKey;

        $requestBody = json_encode([
            'document'     => $this->getBody(),
            'encodingType' => $this->encoding,
        ]);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL            => $apiUrl,
            CURLOPT_POST           => 1,
            CURLOPT_POSTFIELDS     => $requestBody,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
            ],
        ]);

        $result = json_decode(curl_exec($curl));

        curl_close($curl);

        if (!$result) {
            throw new Exception('Cannot connect to Google Cloud Natural Language API');
        }

        if (isset($result->error)) {
            throw new Exception($result->error->code . ': ' . $result->error->message);
        }

        return $result;
    }

    /**
     * getBody
     *
     * Returns the Document body for the API call.
     *
     * @return array
     */
    private function getBody() {
        $body = [
            'type'          => $this->type,
            'content'       => $this->content,
            'gcsContentUri' => $this->gcsContentUri,
        ];

        if ($this->language) {
            $body['language'] = $this->language;
        }

        return $body;
    }

    /**
     * setType
     *
     * Validates and sets the content type of the document.
     *
     * @param $type
     * @throws Exception
     */
    public function setType($type) {
        if (!in_array($type, self::CONTENT_TYPES)) {
            throw new Exception('Wrong content type');
        }

        $this->type = $type;
    }

    /**
     * setLanguage
     *
     * Validates and set the language of the document.
     *
     * @param $language
     * @throws Exception
     */
    public function setLanguage($language) {
        if (!in_array($language, self::SUPPORTED_LANGUAGES)) {
            throw new Exception('Not a supported Language');
        }

        $this->language = $language;
    }

    /**
     * setEncoding
     *
     * Validates and sets the encoding of the document.
     *
     * @param $encoding
     * @throws Exception
     */
    public function setEncoding($encoding) {
        if (!in_array($encoding, self::SUPPORTED_ENCODING)) {
            throw new Exception('Not a supported encoding type');
        }

        $this->encoding = $encoding;
    }

    /**
     * setGcsContentUri
     *
     * Validates and sets the Google Cloud Storage URI.
     * It just validates if it's a valid URL and not if it's a valid Google Storage URI.
     *
     * @param $uri
     * @throws Exception
     */
    public function setGcsContentUri($uri) {
        if (filter_var($uri, FILTER_VALIDATE_URL) === false) {
            throw new Exception('Not a valid URL');
        }

        $this->gcsContentUri = $uri;
    }
}
