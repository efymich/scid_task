<?php


namespace core\Router;


class Route
{
    public static function Get(string $url, string $controller, string $action): void
    {
        $router = new Router($url, $controller, $action);

        RouteContainer::Add($router, 'get');
    }

    public static function Post(string $url, string $controller, string $action): void
    {
        $router = new Router($url, $controller, $action);

        RouteContainer::Add($router, 'post');
    }

    public static function Put(string $url, string $controller, string $action): void
    {
        $router = new Router($url, $controller, $action);

        RouteContainer::Add($router, 'put');
    }

    public static function Delete(string $url, string $controller, string $action): void
    {
        $router = new Router($url, $controller, $action);

        RouteContainer::Add($router, 'delete');
    }
}