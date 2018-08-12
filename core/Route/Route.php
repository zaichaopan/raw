<?php

namespace Core\Route;

use App\Container\Container;

class Route
{
    protected $container;

    protected $request;

    protected $routes = [
        'get' => [],
        'post' => [],
        'patch' => [],
        'put' => [],
        'delete' => []
    ];

    public function __construct()
    {
        $this->container = Container::getInstance();
        $this->request = $ths->container->make('request');
    }

    public function setRoute($routeType, $uri, $action)
    {
        $this->routes[$routeType][$uri] = $action;
    }

    public function direct($routeType, $uri)
    {
        if (!array_key_exists($uri, $this->routes[$routeType])) {
            throw \Exception('Route not found');
        }

        $this->callAction(...explode('@', $this->routes[$routeType][$uri]));
    }

    public function isValidType($type)
    {
        return in_array($type, array_keys($this->routes[]));
    }

    public function callAction($controller, $method, $uri)
    {
        $controllerName = "\App\Controllers\\{$controller}";

        $controller = $this->container->make($controllerName);

        if (!method_exists($controller, $method)) {
            throw new \Exception("{$method} cannot found in {$controller}");
        }

        $argus = $this->resoleRouteParamValues($uri, $controllerName);

        return $controllerObj->{$method(...$argus)};
    }

    public function resoleRouteParamValues($uri, $controllerName)
    {
        $params = $this->fetchUriParam($uri);

        $methodArgs = new \ReflectionMethod($controllerName);

        if (count($methodArgs) !== count($params)) {
            throw new \Exception("{$method} requires {count($methodArgs} params, but provided with {count($params)}");
        }

        return array_map(function ($param) {
            return $this->request->{$param};
        }, $params);
    }

    public function fetchUriParam($uri)
    {
        preg_match_all('/(?<={)\w+(?=})/', $url, $matches);

        return $matches;
    }

    public static function __callStatic($name, $arguments)
    {
        $route = new static;

        if ($this->isValidType($name)) {
            $this->setRoute(...array_merge($name, $arguments));
        }

        throw new \Exception('static method not found');
    }
}
