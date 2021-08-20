<?php


namespace core\Router;


class Router
{
    public string $url;
    public string $controller;
    public string $action;
    public array $data = [];

    public function __construct($url, $controller, $action)
    {
        $this->url = $url;
        $this->controller = $controller;
        $this->action = $action;
    }

    public function Check(string $url): bool
    {
        $serverUrlArr = array_filter(explode('/', $url));
        $routeUrlArr = array_filter(explode('/', $this->url));

        // compare array's lengths

        if (count($serverUrlArr) !== count($routeUrlArr)) {
            return false;
        }

        // compare array's items and add tags into data

        $tags = [];

        foreach ($routeUrlArr as $index => $tag) {
            if ($tag !== $serverUrlArr[$index]) {
                if (preg_match("/{(\w+)}/", $tag, $tags)) {
                    $this->data[$tags[1]] = $serverUrlArr[$index];
                    continue;
                }
                return false;
            }
        }
        return true;
    }

    public function Apply(array $data = [])
    {
        $currentController = new $this->controller();
        $method = $this->action;
        return $currentController->$method(array_merge($this->data, $data));
    }
}