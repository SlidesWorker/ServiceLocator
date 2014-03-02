<?php

namespace SlidesWorkerTest\ServiceLocator;

use SlidesWorkerTest\ServiceLocator\TestAsset\InitializerAwareClass;
use SlidesWorker\ServiceLocator\Initializer\ServiceLocatorInitializer;
use SlidesWorkerTest\ServiceLocator\TestAsset\ServiceLocatorAwareClass;
use SlidesWorker\ServiceLocator\Initializer\InitializerAwareInterface;
use SlidesWorker\ServiceLocator\ServiceLocatorInterface;
use SlidesWorker\ServiceLocator\ServiceLocatorAwareInterface;

class InitializerAwareTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \SlidesWorker\ServiceLocator\Initializer\InitializerAwareInterface
     */
    protected $SUT;
    protected $initializer;

    public function setup()
    {
        $this->SUT = new InitializerAwareClass();
    }

    public function testInitialzierTopOfStack ()
    {
        $classInit = new ServiceLocatorInitializer();
        $callbackTop = function ($instance, ServiceLocatorInterface $locator) {
            $this->assertNull($instance->getServiceLocator());
        };
        $callbackBottom = function ($instance, ServiceLocatorInterface $locator) {
            $this->assertSame($locator, $instance->getServiceLocator());
        };

        $instance = new ServiceLocatorAwareClass();

        $this->SUT->addInitializer($classInit);
        $this->SUT->addInitializer($callbackBottom);
        $this->SUT->addInitializer($callbackTop, true);

        $this->SUT->callInitializers($instance);

    }

    /**
     * @dataProvider dataProviderTestInitialzier
     */
    public function testInitialzier ($initializerArray, $exception = null)
    {
        if ($exception !== null) {
            $this->setExpectedException($exception);
        }
        $instance = new ServiceLocatorAwareClass();

        foreach ($initializerArray as $initializer) {
            $this->SUT->addInitializer($initializer);
        }

        $this->SUT->callInitializers($instance);

        if ($exception === null) {
            $this->assertInstanceOf('SlidesWorker\ServiceLocator\ServiceLocatorAwareInterface', $instance);

            $this->assertSame($this->SUT, $instance->getServiceLocator());
        }
    }

    public function dataProviderTestInitialzier ()
    {
        $callbackInit= function ($instance, ServiceLocatorInterface $locator) {
            if ($instance instanceof ServiceLocatorAwareInterface ||
                $instance instanceof ServiceLocatorAwareTrait
            ) {
                $instance->setServiceLocator($locator);
            }

            return $instance;
        };
        $classInit = new ServiceLocatorInitializer();

        return array(
            array(array($classInit)),
            array(array('\SlidesWorker\ServiceLocator\Initializer\ServiceLocatorInitializer')),
            array(array($callbackInit)),
            array(array($classInit,$callbackInit)),

            array(array('\SlidesWorkerTest\ServiceLocator\TestAsset\InitializerNotExists'),
                  '\SlidesWorker\ServiceLocator\Exception\InvalidArgumentException'),
        );
    }
}
