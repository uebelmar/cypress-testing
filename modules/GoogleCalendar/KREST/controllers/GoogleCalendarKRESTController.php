<?php
namespace SpiceCRM\modules\GoogleCalendar\KREST\controllers;

use Exception;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\modules\GoogleCalendar\GoogleCalendar;
use SpiceCRM\includes\authentication\AuthenticationController;
use SpiceCRM\modules\GoogleCalendar\GoogleCalendarRestHandler;

class GoogleCalendarKRESTController
{

    /**
     * Returns the Exchange sync config for the given User.
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function getConfiguration($req, $res, $args) {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();

        // check if we have a userid if not take current user
        $user_id = $args['userid'] ?: $current_user->id;

        // instantiate new calendar to check access
        $calendar = new GoogleCalendar($user_id);

        return $res->withJson([
            'accessible' => $calendar->checkAccess(),
            'cansubscribe' => !empty(SpiceConfig::getInstance()->config['googleapi']['notificationhost']),
            'userconfig' => [$db->fetchByAssoc($db->query("SELECT * FROM sysgsuiteuserconfig WHERE user_id='$user_id'"))],
            'subscriptions' => [$db->fetchByAssoc($db->query("SELECT * FROM sysgsuiteusersubscriptions WHERE user_id='$user_id'"))]
        ]);
    }

    /**
     * Returns the Exchange sync config for the given User.
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function startSubscription($req, $res, $args) {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();

        // instantiate new calendar to check access
        $calendar = new GoogleCalendar($args['userid']);
        $subscription = $calendar->startSubscription();

        if($subscription === true){
            $db->query("INSERT INTO sysgsuiteuserconfig (id, user_id, scope) VALUES('".create_guid()."', '{$args['userid']}', 'Calendar')");
        } else {

        }

        return $res->withJson([
            'userconfig' => [$db->fetchByAssoc($db->query("SELECT * FROM sysgsuiteuserconfig WHERE user_id='{$args['userid']}'"))],
            'subscriptions' => [$db->fetchByAssoc($db->query("SELECT * FROM sysgsuiteusersubscriptions WHERE user_id='{$args['userid']}'"))]
        ]);
    }
    /**
     * Returns the Exchange sync config for the given User.
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function stopSubscription($req, $res, $args) {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();

        // instantiate new calendar to check access
        $calendar = new GoogleCalendar($args['userid']);
        $subscription = $calendar->stopSubscription();
        if($subscription === true){
            $db->query("DELETE FROM sysgsuiteuserconfig WHERE user_id = '{$args['userid']}' AND scope = 'Calendar'");
        }

        return $res->withJson([
            'userconfig' => [$db->fetchByAssoc($db->query("SELECT * FROM sysgsuiteuserconfig WHERE user_id='{$args['userid']}'"))],
            'subscriptions' => [$db->fetchByAssoc($db->query("SELECT * FROM sysgsuiteusersubscriptions WHERE user_id='{$args['userid']}'"))]
        ]);
    }

    /**
     * Handles the incoming sync requests from the Exchange server.
     *
     * @param $req
     * @param $res
     * @param $args
     * @throws Exception
     */
    public function handle($req, $res, $args) {
        $db = DBManagerFactory::getInstance();

        $GLOBALS['gsuiteinbound'] = true;

        $headers = getallheaders();

        $userRecord = $db->fetchByAssoc($db->query("SELECT * FROM sysgsuiteusersubscriptions WHERE subscriptionid='{$headers['X-Goog-Channel-ID']}' AND resourceid='{$headers['X-Goog-Resource-ID']}'"));
        if($userRecord){
            $calendar = new GoogleCalendar($userRecord['user_id']);
            $calendar->syncGcal2Spice($userRecord['user_id']);
        }
    }

    /**
     * gets a new calendar bean
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function GoogleCalendarGetBeans($req, $res, $args){
        $handler = new GoogleCalendarRestHandler();
        $result = $handler->getBeans();
        return $res->withJson($result);

    }

    /**
     * gets a new calendar
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function GoogleCalendarGetCalendar($req, $res, $args){
        $handler = new GoogleCalendarRestHandler();
        $result = $handler->getCalendars($req->getQueryParams());
        return $res->withJson($result);
    }

    /**
     * get the calendar bean mapping
     * @param $req
     * @param $res
     * @param $args
     */

    public function GoogleCalendarGetBeanMapping($req, $res, $args){
        $handler = new GoogleCalendarRestHandler();
        $result = $handler->getBeanMappings($req->getQueryParams());
        return $res->withJson($result);
    }

    /**
     * saves the calender bean mapping
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function GoogleCalendarSaveMapping($req, $res, $args){
        $handler = new GoogleCalendarRestHandler();
        $result = $handler->saveBeanMappings($req->getParsedBody());
        return $res->withJson($result);

    }

    /**
     * synchronize the google calendar
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function GoogleCalendarSync($req, $res, $args){
        $handler = new GoogleCalendarRestHandler();
        $result = $handler->synchronize($req->getParsedBody());
        return $res->withJson($result);
    }

    /**
     * get google calendar events
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */

    public function GoogleCalendarGetEvents($req, $res, $args){
        $handler = new GoogleCalendarRestHandler();
        $result = $handler->getGoogleEvents($req->getQueryParams());
        return $res->withJson($result);

    }

}
