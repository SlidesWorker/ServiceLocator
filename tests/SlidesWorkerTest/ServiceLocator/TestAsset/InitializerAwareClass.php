<?php
namespace SlidesWorkerTest\ServiceLocator\TestAsset;

use SlidesWorker\ServiceLocator\Initializer\InitializerAwareInterface;
use SlidesWorker\ServiceLocator\Initializer\InitializerAwareTrait;
use SlidesWorker\ServiceLocator\ServiceLocator;
use SlidesWorker\ServiceLocator\ServiceLocatorInterface;

class InitializerAwareClass implements InitializerAwareInterface, ServiceLocatorInterface
{
    use InitializerAwareTrait;

    protected $foo = 'bar';

    public function getFoo()
    {
        return $this->foo;
    }

    public function setFoo($foo)
    {
        $this->foo = $foo;
    }



    public function get($name)
    {
        // TODO: Auto-generated method stub
    }
    public function set($name, $instance)
    {
        // TODO: Auto-generated method stub
    }
    public function has($name)
    {
        // TODO: Auto-generated method stub
    }
}
