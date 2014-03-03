<?php

namespace Example;

use SlidesWorker\ServiceLocator\ServiceLocator;
use SlidesWorker\ServiceLocator\ServiceLocatorInterface;
use SlidesWorker\ServiceLocator\ServiceLocatorAwareInterface;
use SlidesWorker\ServiceLocator\ServiceLocatorAwareTrait;
use SlidesWorker\ServiceLocator\Initializer\InitializerInterface;

class ServiceCanHoldService2 implements ServiceTwoAwareInterface
{
    protected $service2;
    public function setService2($service2)
    {
        $this->service2  = $service2;
    }
    public function getService2()
    {
        return $this->service2;
    }
}

interface ServiceTwoAwareInterface
{
    public function setService2($service2);
    public function getService2();
}
class Service2
{
    public function foo()
    {
    }
}
class Service2Initializer implements InitializerInterface
{
    public function initialize($intance, ServiceLocatorInterface $locator)
    {
        if ($intance instanceof ServiceTwoAwareInterface) {
            $intance->setService2($locator->get('service2'));
        }
    }
}




// setup ServiceLocator
$serviceLocator = new ServiceLocator();

// Hold with trait
$serviceLocator->addInitializer(new Service2Initializer());

// Hold with trait
$serviceLocator->setInvokable('service1', '\Example\ServiceCanHoldService2');
$serviceLocator->setInvokable('service2', '\Example\Service2');


// get Services
$service1 = $serviceLocator->get('service1');

if ($service1->getService2() !== $serviceLocator->get('service2')) {
    die('fail');
}
echo "done";
