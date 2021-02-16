<?php
namespace SpiceCRM\includes\SpiceCRMExchange;

use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseItemIdsType;
use jamesiarmes\PhpEws\Enumeration\DefaultShapeNamesType;
use jamesiarmes\PhpEws\Enumeration\MapiPropertyTypeType;
use jamesiarmes\PhpEws\Request\GetItemType;
use jamesiarmes\PhpEws\Type\ItemIdType;
use jamesiarmes\PhpEws\Type\ItemResponseShapeType;
use jamesiarmes\PhpEws\Type\PathToExtendedFieldType;
use jamesiarmes\PhpEws\Enumeration\UserConfigurationPropertyType;
use jamesiarmes\PhpEws\Request\GetUserConfigurationType;
use jamesiarmes\PhpEws\Enumeration\ResponseClassType;
use jamesiarmes\PhpEws\Type\UserConfigurationNameType;
use jamesiarmes\PhpEws\Type\DistinguishedFolderIdType;
use jamesiarmes\PhpEws\Enumeration\DistinguishedFolderIdNameType;
use SimpleXMLElement;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use User;
use SpiceCRM\includes\SpiceCRMExchange\Exceptions\MissingEwsCredentialsException;

class SpiceCRMExchangeConnector
{
    public $client;
    private $logger;

    public function __construct(User $user) {
        $this->logger = new SpiceCRMExchangeLogger();
        // set field where to find exchange username (usually also Active Directory name).
        // Spice Standard is CRM user name = AD user name
        // customization per config possible
        $username = $user->user_name;
        if(isset(SpiceConfig::getInstance()->config['SpiceCRMExchange']['username_location'])){
            $username = $user->{SpiceConfig::getInstance()->config['SpiceCRMExchange']['username_location']};
        }
        if(isset($GLOBALS['spice_config']['SpiceCRMExchange']['username_location'])){
            $username = $user->{$GLOBALS['spice_config']['SpiceCRMExchange']['username_location']};
        }
        $this->client = new SpiceCRMExchangeClient($username);
    }

    public function getItem($itemId, $changeKey) {
        $this->logger->logInboundRecord(
            $itemId,
            '',
            '',
            'Getting Item from EWS.'
        );
        $itemrequest = new GetItemType();
        $itemrequest->ItemShape = new ItemResponseShapeType();
        $itemrequest->ItemShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;
        $itemrequest->ItemIds = new NonEmptyArrayOfBaseItemIdsType();
        $itemrequest->ItemIds->ItemId = new ItemIdType();
        $itemrequest->ItemIds->ItemId->Id = $itemId;
        $itemrequest->ItemIds->ItemId->ChangeKey = $changeKey;

        $extendedField = new PathToExtendedFieldType();
        $extendedField->PropertyType = MapiPropertyTypeType::STRING;
        $extendedField->PropertySetId = '00020329-0000-0000-C000-000000000046';
        $extendedField->PropertyName = self::getExtendedFieldName();
        $itemrequest->ItemShape->AdditionalProperties->ExtendedFieldURI = $extendedField;

        $itemresponse = $this->client->request('GetItem', $itemrequest);
        $this->logger->logInboundRecord(
            $itemId,
            '',
            '',
            'GetItem response => '.print_r($itemresponse, true)
        );

        if ($itemresponse->ResponseMessages->GetItemResponseMessage[0]->Items->CalendarItem[0]) {
            return $itemresponse->ResponseMessages->GetItemResponseMessage[0]->Items->CalendarItem[0];
        }
        // todo add ifs for contacts and tasks

        return $itemresponse->ResponseMessages->GetItemResponseMessage[0];
    }

    public static function getExtendedFieldName() {
        
        return 'cecp-' . SpiceConfig::getInstance()->config['SpiceCRMExchange']['guid'];
    }

    /**
     * Extracts the notification from the incoming request from an EWS server and returns it in XML format.
     *
     * @param $body
     * @return SimpleXMLElement
     */
    public static function extractNotification($body) {
        // ugly but seemingly required to make it readable
        // ToDo: check if this can be done better with namespaces registered
        $xml = str_replace(["\n", "soap11:", "m:", "t:"], '', $body);
        $newXML = simplexml_load_string($xml);
        return $newXML->Body->SendNotification->ResponseMessages->SendNotificationResponseMessage->Notification;
    }

    /**
     * Responds to the EWS server in a format required by it.
     *
     * @param $subscription
     */
    public static function respondToEws($subscription) {
        $responseOK = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope"><soap:Body><SendNotificationResult xmlns="http://schemas.microsoft.com/exchange/services/2006/messages"><SubscriptionStatus>' . ($subscription ? 'OK' : 'Unsubscribe') . '</SubscriptionStatus></SendNotificationResult></soap:Body></soap:Envelope>';
        echo $responseOK;
    }

    public function checkConnection() {
        try {
            $this->client->checkConfiguration();

            $request = new GetUserConfigurationType();
            $request->UserConfigurationProperties = UserConfigurationPropertyType::ALL;

            $name = new UserConfigurationNameType();
            $name->DistinguishedFolderId = new DistinguishedFolderIdType();
            $name->DistinguishedFolderId->Id = DistinguishedFolderIdNameType::ROOT;
            $name->Name = 'OWA.UserOptions';
            $request->UserConfigurationName = $name;

            $response = $this->client->request('GetUserConfiguration', $request);

            $response_messages = $response->ResponseMessages->GetUserConfigurationResponseMessage;
            foreach ($response_messages as $response_message) {
                // Make sure the request succeeded.
                if ($response_message->ResponseClass != ResponseClassType::SUCCESS) {
                    $code = $response_message->ResponseCode;
                    $message = $response_message->MessageText;
                    return false;
                }
            }

            return true;
        } catch (MissingEwsCredentialsException $e) {
            return false;
        }
    }
}
