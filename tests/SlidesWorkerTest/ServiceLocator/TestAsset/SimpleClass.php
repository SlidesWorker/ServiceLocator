<?php
namespace SlidesWorkerTest\ServiceLocator\TestAsset;

use SlidesWorker\ServiceLocator\ServiceLocatorAwareInterface;

class SimpleClass
{
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
