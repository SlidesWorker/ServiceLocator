<?php
namespace SlidesWorker\ServiceLocator;

interface ServiceLocatorInterface
{
    public function get($name);

    public function set($name, $instance);

    public function has($name);
}
