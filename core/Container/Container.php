<?php

namespace Core\Container;

use Closure;
use Exception;

class Container
{
    /**
     * The current globally container instance.
     *
     * @var self
     */
    protected static $instance;

    /**
     * Store bindings.
     *
     * @var array
     */
    protected $bindings = [];

    /**
     * Store shared instances
     *
     * @var array
     */
    protected $instances = [];

    public function isShared(string $abstract): bool
    {
        return $this->bindings[$abstract]['shared'];
    }

    public function isNotShared(string $abstract): bool
    {
        return !$this->isShared($abstract);
    }

    public function hasBinding(string $abstract): bool
    {
        return array_key_exists($abstract, $this->bindings);
    }

    public function hasInstance(string $abstract): bool
    {
        return array_key_exists($abstract, $this->instances);
    }

    public function bind($abstract, Closure $concrete, bool $shared = false): object
    {
        $this->bindings[$abstract] = compact('concrete', 'shared');
        return $this;
    }

    public function instance(string $abstract, object $instance) : self
    {
        $this->instances[$abstract] = $instance;
        return $this;
    }

    public function singleton($abstract, Closure $concrete) : self
    {
        $this->bind($abstract, $concrete, true);
        return $this;
    }

    public function make(string $abstract): object
    {
        // Bind to closure
        if ($this->hasBinding($abstract)) {
            // It is not shared; Using closure to create a new instance.
            if ($this->isNotShared($abstract)) {
                $resolved = $this->bindings[$abstract]['concrete'];
                return $resolved();
            }

            // It is shared
            // It is already resolved in instances
            if ($this->hasInstance($abstract)) {
                return $this->instances[$abstract];
            }

            // It doesn't not store in instances. Resolved and store in instances because it is shared
            $resolved = $this->bindings[$abstract]['concrete'];
            return $this->instances[$abstract] = $resolved();
        }

        // Bind to instance
        if ($this->hasInstance($abstract)) {
            return $this->instances[$abstract];
        }

        // Try auto Resolve
        $resolved = $this->autoResolve($abstract);

        return $resolved;
    }

    protected function autoResolve(string $abstract): object
    {
        if (!class_exists($abstract)) {
            throw new EntryNotFoundException("Class {$abstract} does not exists!");
        }

        $class = new \ReflectionClass($abstract);

        if (!$class->isInstantiable()) {
            throw new EntryNotFoundException("Class {$abstract} cannot be instantiated!");
        }

        $constructor = $class->getConstructor();

        if (!$constructor) {
            return new $abstract;
        }

        $parameters = $constructor->getParameters();

        $args = [];

        try {
            foreach ($parameters as $parameter) {
                if (!$paramClassInfo = $parameter->getClass()) {
                    throw new Exception;
                }

                $args[] = $this->make($paramClassInfo->getName());
            }
        } catch (Exception $e) {
            throw new EntryNotFoundException("Cannot resolve complex class {$abstract}");
        }

        return $class->newInstanceArgs($args);
    }

    public static function getInstance(): self
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }
}
