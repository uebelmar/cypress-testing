<?php

namespace SpiceCRM\includes\StarFaceVOIP;

use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\VoiceOverIP\VoiceOverIP;

class StarFaceVOIP extends VoiceOverIP
{
    protected $preferenceName     = 'starface';
    protected $preferenceCategory = 'starface';
    protected $channelPrefix      = 'starface';

    public function __construct()
    {
        
        $this->config = SpiceConfig::getInstance()->config['starface'];
    }

    /**
     * {@inheritdoc}
     *
     * @param $msisdn
     * @return bool
     */
    public function initiateCall($msisdn)
    {

        $connector = $this->getNewConnector();
        $sfcall = $connector->initiateCall($msisdn);

        // write teh call to the database
        if ($sfcall !== false) {
            $prefs = $this->getPreferences();

            $call = $this->createCall(
                $prefs['username'],
                self::DIRECTION_OUTGOING,
                $sfcall,
                'PROCEEDING',
                $prefs['username'],
                $msisdn,
                ''
            );

            $this->writeCall($call);
        }

        return $sfcall;
    }

    /**
     * sets the users preferences
     *
     * @param $prefs
     * @return bool
     */
    public function setPreferences($prefs)
    {
        $prefs['userpass'] = hash('sha512', $prefs['userpass']);

        parent::setPreferences($prefs);
    }

    /**
     * subscribes to the events on the PBX
     *
     * @return bool
     */
    public function subscribeEvents()
    {
        if ($this->config['socketurl_backend']) {
            $connector = $this->getNewConnector();
            return $connector->subscribeEvents();
        }

        return false;
    }

    /**
     * subscribes to the events on the PBX
     *
     * @return bool
     */
    public function unsubscribeEvents()
    {
        $connector = $this->getNewConnector();
        return $connector->unsubscribeEvents();
    }

    /**
     * handles an incoming Event
     *
     * @param $starfaceUser
     * @param $event
     */
    public function handleEvent($starfaceUser, $event)
    {
        $eventArray = json_decode(json_encode((array)$event), true);
        $thisCall = $this->getCall($this->getEventMemberByName($eventArray['params']['param']['value']['struct']['member'], 'id'));
        // ($channel, $direction, $id, $state, $callernumber, $callednumber
        $call = $this->createCall(
            $starfaceUser,
            $thisCall['calldirection'],
            $this->getEventMemberByName($eventArray['params']['param']['value']['struct']['member'], 'id'),
            $this->getEventMemberByName($eventArray['params']['param']['value']['struct']['member'], 'state'),
            $this->getEventMemberByName($eventArray['params']['param']['value']['struct']['member'], 'callerNumber'),
            $this->getEventMemberByName($eventArray['params']['param']['value']['struct']['member'], 'calledNumber'),
            $eventArray['methodName']
        );

        if ($call->direction && $call->callernumber && $call->callednumber) {
            // write to the database
            $this->writeCall($call);
            // post to the nodejs server
            $this->notifySocket($call->channel, $call);
        }
    }

    /**
     * returns an event meber field value by the name
     *
     * @param $eventMembers
     * @param $name
     * @return mixed|string
     */
    private function getEventMemberByName($eventMembers, $name)
    {
        foreach ($eventMembers as $thisMember) {
            if ($thisMember['name'] == $name) {
                return is_string($thisMember['value']['string']) ? $thisMember['value']['string'] : '';
            }
        }
    }

    /**
     * returves the channel from teh call
     *
     * @param $callId
     * @return mixed
     */
    private function getChannel($callId)
    {
        $db = DBManagerFactory::getInstance();
        $record = $db->fetchByAssoc($db->query("SELECT channel FROM starfacecalls WHERE id = '$callId'"));
        return $record['channel'];
    }

    /**
     * {@inheritdoc}
     * @return mixed|StarFaceVOIPConnector
     */
    protected function getNewConnector()
    {
        return new StarFaceVOIPConnector();
    }

    protected function createCall($channel, $direction, $id, $state, $callernumber, $callednumber, $event)
    {

        $call = parent::createCall($channel, $direction, $id, $state, $callernumber, $callednumber, $event);

        if (empty($call->direction)) {
            switch ($call->state) {
                case 'INCOMING':
                case 'RINGING':
                    $call->direction = self::DIRECTION_INCOMING;
                    break;
                case 'PROCEEDING':
                case 'RINGBACK':
                    $call->direction = self::DIRECTION_OUTGOING;
                    break;
            }
        }

        return $call;
    }
}
