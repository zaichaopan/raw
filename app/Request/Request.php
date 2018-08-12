<?php

namespace App\Request;

class Request
{
    public function uri()
    {
        return trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    }

    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function __get(string $name)
    {
        if (isset($_REQUEST[$name])) {
            return $_REQUEST[$name];
        }

        return null;
    }

    public function exist(): bool
    {
        return isset($_REQUEST[$name]);
    }

    public function get(string $name, $defaultValue)
    {
        if ($this->exist($name)) {
            return $this->{$name};
        }

        return $defaultValue;
    }
}
