<?php
namespace SlidesWorkerTest\ServiceLocator\TestAsset;

use SlidesWorker\ServiceLocator\ServiceLocator;
use SlidesWorker\ServiceLocator\FactoryInterface;
use SlidesWorker\ServiceLocator\ServiceLocatorInterface;

class SimpleFactoryClass implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $locator, $cName, $rName)
    {
        $instance = new SimpleClass();

        return $instance;
    }
}
