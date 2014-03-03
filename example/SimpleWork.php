<?php

namespace Example;

use SlidesWorker\ServiceLocator\ServiceLocator;

class Service1
{
}

class Service2
{
}


// setup ServiceLocator
$serviceLocator = new ServiceLocator();


$serviceLocator->set('service1', new Service1());
$serviceLocator->set('service2', new Service2());

$service1 = $serviceLocator->get('service1');
$service2 = $serviceLocator->get('service2');
