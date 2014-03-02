<?php
namespace SlidesWorker\ServiceLocator;

interface FactoryInterface
{
    public function createService(ServiceLocatorInterface $locator, $cName, $rName);
}
