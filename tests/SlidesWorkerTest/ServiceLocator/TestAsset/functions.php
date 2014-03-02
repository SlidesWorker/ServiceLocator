<?php
namespace SlidesWorkerTest\ServiceLocator\TestAsset;

use SlidesWorker\ServiceLocator\ServiceLocator;

function SimpleFactoryCallbackFunction(ServiceLocator $locator, $cName, $rName)
{
    $instance = new SimpleClass();
    return $instance;
}
