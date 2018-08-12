<?php

namespace Core\Request;

class Request
{
    protected $request;

    protected $server;

    public function __construct(array $request = [], array $server = [])
    {
        $this->request = $request ?: $_REQUEST;
        $this->server = $server ?: $_SERVER;
    }

    public function uri()
    {
        return trim(parse_url($this->server['REQUEST_URI'], PHP_URL_PATH), '/');
    }

    public function method()
    {
        return $this->server['REQUEST_METHOD'];
    }

    public function __get(string $name)
    {
        return $this->get($name, null);
    }

    public function exist($key): bool
    {
        return array_key_exists($this->request, $key);
    }

    public function get(string $name, $default)
    {
        return array_key_exists($this->request, $key) ? $this->request[$key] : $default;
    }
}
