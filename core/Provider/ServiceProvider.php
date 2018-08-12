<?php

namespace Core\Provider;

use App\Container\Container;

abstract class ServiceProvider
{
    protected $app;

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    public function app()
    {
        return $this->app;
    }

    abstract public function boot();

    abstract public function register();
}
