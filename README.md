
SlidesWorker - ServiceLocator
=============================

[![Latest Stable Version](https://poser.pugx.org/slidesworker/servicelocator/v/stable.png)](https://packagist.org/packages/slidesworker/servicelocator)


[![Build Status](https://travis-ci.org/SlidesWorker/ServiceLocator.png?branch=master)](https://travis-ci.org/SlidesWorker/ServiceLocator)
[![Code Coverage](https://scrutinizer-ci.com/g/SlidesWorker/ServiceLocator/badges/coverage.png?s=7d83e2b5c48283cf20366c0d5e4b2892cb7102e3)](https://scrutinizer-ci.com/g/SlidesWorker/ServiceLocator/)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/SlidesWorker/ServiceLocator/badges/quality-score.png?s=8b5b1259f4e1f646f71028df60433980ed5ca12b)](https://scrutinizer-ci.com/g/SlidesWorker/ServiceLocator/)
[![Dependency Status](https://www.versioneye.com/user/projects/531839a9ec13755bfa00063d/badge.png)](https://www.versioneye.com/user/projects/531839a9ec13755bfa00063d)


How to use
==========


simple use
------------

You can simple register your service to this ServiceLocator
Fore more example see [this file](https://github.com/SlidesWorker/ServiceLocator/blob/master/example/SimpleWork.php)

```php
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
```



with factory
------------

The ServiceLocator can work with a some few factories. Classes that implements ```SlidesWorker\ServiceLocator\FactoryInterface``` and function or closure.
Fore more example see [this file](https://github.com/SlidesWorker/ServiceLocator/blob/master/example/WorkWithFactory.php)

```php
namespace Example;

use SlidesWorker\ServiceLocator\ServiceLocator;
use SlidesWorker\ServiceLocator\ServiceLocatorInterface;

class Service {}

// setup ServiceLocator
$serviceLocator = new ServiceLocator();


// factory as closure
$serviceLocator->setFactory('service', function (ServiceLocatorInterface $locator) {
    return Service();
});

// get a service
$service = $serviceLocator->get('service');
```

Initialzer and ServiceLocator
-----------------------------

If you have the need that one objects must hold some other object Initializer is the right feature.
In the case that ServiceLocator create the Object for you it run a Stack of few Initializer.
You can add your own Initializer to this system, too.

A working example you find [here](https://github.com/SlidesWorker/ServiceLocator/blob/master/example/WorkWithInitializer.php)





give service the ability to handle the ServiceLocator
-----------------------------------------------------

ServiceLocator has the ability to add him self to a object. For this feature the object must be a instance of
```SlidesWorker\ServiceLocator\ServiceLocatorAwareTrait``` or ```SlidesWorker\ServiceLocator\ServiceLocatorAwareInterface```

For more example see [this file](https://github.com/SlidesWorker/ServiceLocator/blob/master/example/ServiceCanHoldServiceLocator.php)

```php
namespace Example;

use SlidesWorker\ServiceLocator\ServiceLocator;
use SlidesWorker\ServiceLocator\ServiceLocatorInterface;
use SlidesWorker\ServiceLocator\ServiceLocatorAwareInterface;
use SlidesWorker\ServiceLocator\ServiceLocatorAwareTrait;

// only php 5.4 and higher
class ServiceCanHoldServiceLocator1
{
    use ServiceLocatorAwareTrait;
}


// setup ServiceLocator
$serviceLocator = new ServiceLocator();

// factory as closure
$serviceLocator->setFactory('service', function (ServiceLocatorInterface $locator) {
    return new ServiceCanHoldServiceLocator1();
});

$serviceLocator->get('service');
```