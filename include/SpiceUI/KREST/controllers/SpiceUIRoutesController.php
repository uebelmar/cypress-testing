<?php

namespace SpiceCRM\includes\SpiceUI\KREST\controllers;

use SpiceCRM\includes\database\DBManagerFactory;

class SpiceUIRoutesController
{
    static function getRoutesDirect()
    {
        $db = DBManagerFactory::getInstance();
        $routeArray = array();
        $routes = $db->query("SELECT * FROM sysuiroutes");
        while ($route = $db->fetchByAssoc($routes)) {

            $routeArray[] = $route;

        }
        $routes = $db->query("SELECT * FROM sysuicustomroutes");
        while ($route = $db->fetchByAssoc($routes)) {

            $routeArray[] = $route;

        }
        return $routeArray;
    }

    static function getRoutes($req, $res, $args)
    {
        $db = DBManagerFactory::getInstance();
        $routeArray = array();
        $routes = $db->query("SELECT * FROM sysuiroutes");
        while ($route = $db->fetchByAssoc($routes)) {

            $routeArray[] = $route;

        }
        $routes = $db->query("SELECT * FROM sysuicustomroutes");
        while ($route = $db->fetchByAssoc($routes)) {

            $routeArray[] = $route;

        }
        return $res->withJson($routeArray);
    }
}