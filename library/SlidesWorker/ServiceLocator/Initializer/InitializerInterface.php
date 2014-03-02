<?php
namespace SlidesWorker\ServiceLocator\Initializer;

use SlidesWorker\ServiceLocator\ServiceLocatorInterface;

interface InitializerInterface
{
    public function initialize($intance, ServiceLocatorInterface $locator);
}
