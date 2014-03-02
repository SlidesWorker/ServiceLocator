<?php
namespace SlidesWorkerTest\ServiceLocator\TestAsset;

use SlidesWorker\ServiceLocator\ServiceLocatorAwareInterface;
use SlidesWorker\ServiceLocator\ServiceLocatorAwareTrait;

class ServiceLocatorAwareClass implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $foo = 'bar';

    public function getFoo()
    {
        return $this->foo;
    }

    public function setFoo($foo)
    {
        $this->foo = $foo;
    }
}
