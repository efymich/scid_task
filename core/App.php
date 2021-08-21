<?php

namespace core;

use core\Response\Response_interface\iResponse;
use core\Router\RouteContainer;

require "database.php";
require "helpers.php";

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

class App
{
    public static function Init()
    {
        /**
         * @var $response_obj iResponse
         */
        require '../route/roadmap.php';
        $url = $_SERVER['REQUEST_URI'];
        $type = strtolower($_SERVER['REQUEST_METHOD']);
        $response_obj = RouteContainer::Searcher($url, $type);
        $response_obj->giveResponse();
        databaseClose();
    }

}