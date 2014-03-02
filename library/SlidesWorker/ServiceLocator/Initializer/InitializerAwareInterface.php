<?php
namespace SlidesWorker\ServiceLocator\Initializer;

interface InitializerAwareInterface
{
    public function addInitializer($initializer, $topOfStack = false);
    public function callInitializers($instance);
}
