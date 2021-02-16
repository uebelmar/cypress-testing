<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\includes\SpiceReminders\KREST\controllers;

use SpiceCRM\includes\SpiceReminders\SpiceReminders;

class SpiceRemindersKRESTController{

    static function getReminders($req, $res, $args) {
        return $res->withJson(SpiceReminders::getRemindersRaw('', 0));
    }

    static function addReminder($req, $res, $args){
        SpiceReminders::setReminderRaw($args['id'], $args['module'], $args['date']);
        return $res->withJson(['status' => 'success']);
    }

     static function deleteReminder($req, $res, $args) {
         SpiceReminders::removeReminder($args['id']);
        return $res->withJson(['status' => 'success']);
    }
}
