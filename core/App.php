<?php

namespace core;

use core\Response\Response_interface\iResponse;
use core\Router\RouteContainer;

require "database.php";
require "helpers.php";


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