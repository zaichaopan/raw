<?php

use Core\Container\Container;
use PHPUnit\Framework\TestCase;
use Core\Container\EntryNotFoundException;

class ContainerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->container = new Container;
    }

    /** @test */
    public function it_can_resolve_binding()
    {
        $this->container->bind('foo', function () { return new Foo; });

        $this->assertInstanceOf(Foo::class, $foo = $this->container->make('foo'));

        $this->assertNotSame($foo, $this->container->make('foo'));
    }

    /** @test */
    public function it_can_resolve_instance()
    {
        $this->container->instance('foo', new Foo);

        $this->assertInstanceOf(Foo::class, $foo = $this->container->make('foo'));

        $this->assertSame($foo, $this->container->make('foo'));
    }

    /** @test */
    public function it_can_resolve_singleton()
    {
        $this->container->singleton('foo', function () {
            return new Foo;
        });

        $this->assertInstanceOf(Foo::class, $foo = $this->container->make('foo'));
        $this->assertSame($foo, $this->container->make('foo'));
    }

    /** @test */
    public function it_can_auto_resolve_complex_instance()
    {
        $dummy = $this->container->make(Dummy::class);

        $this->assertInstanceOf(Dummy::class, $dummy);
        $this->assertInstanceOf(Foo::class, $dummy->foo);
        $this->assertInstanceOf(Bar::class, $dummy->bar);
    }

    /** @test */
    public function it_will_throws_entry_no_found_exception_if_class_not_found_when_resolving()
    {
        $this->expectException(EntryNotFoundException::class);
        $this->container->make('fooBar');
    }

    /** @test */
    public function it_will_throws_entry_not_found_exception_if_cannot_resolve_complex_instance()
    {
        $this->expectException(EntryNotFoundException::class);
        $this->container->make(Yummy::class);
    }

    /** @test */
    public function it_can_resolve_current_globally_container()
    {
        $globalContainer = Container::getInstance();
        $this->assertInstanceOf(Container::class, $globalContainer);
        $this->assertSame($globalContainer, Container::getInstance());
    }
}

class Foo
{
}

class Bar
{
}

class Dummy
{
    public $foo;

    public $bar;

    public function __construct(Foo $foo, Bar $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }
}

class Yummy
{
    public $yummy;

    public function __construct($yummy)
    {
        $this->yummy = $yummy;
    }
}
