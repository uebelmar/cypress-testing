<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\includes\Five9VOIP;

use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\VoiceOverIP\VoiceOverIP;

class Five9VOIP extends VoiceOverIP
{
    protected $preferenceName     = 'five9';
    protected $preferenceCategory = 'five9';
    protected $channelPrefix      = 'five9';

    var $connector;

    public function __construct()
    {
        global $sugar_config;
        $this->config = $sugar_config['five9'];
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
        return($connector->initiateCall($msisdn));
    }

    /**
     * sets the users preferences
     *
     * @param $prefs
     * @return bool
     */
    public function setPreferences($prefs)
    {
        $prefs['userpass'] = $prefs['userpass'];

        return parent::setPreferences($prefs);
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
     * @return mixed|Five9VOIPConnector
     */
    protected function getNewConnector()
    {
        if(!$this->connector){
            $this->connector = new Five9VOIPConnector();
        }
        return $this->connector;
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
