<?php

namespace SlidesWorkerTest\ServiceLocator;

use SlidesWorker\ServiceLocator\ServiceLocator;
use SlidesWorker\ServiceLocator\ServiceLocatorAwareInterface;

class ServiceLocatorTest extends \PHPUnit_Framework_TestCase
{
    protected $SUT;

    public static function setUpBeforeClass()
    {
        require_once 'TestAsset/functions.php';
    }

    public function setup()
    {
        $this->SUT = new ServiceLocator();

        $this->SUT->setFactory('SimpleFactoryInstance', new TestAsset\SimpleFactoryClass());
        $this->SUT->setFactory('SimpleFactoryClass', '\SlidesWorkerTest\ServiceLocator\TestAsset\SimpleFactoryClass');
        $this->SUT->setFactory('SimpleFactoryCallback', function (ServiceLocator $locator, $cName, $rName) {
            $instance = new TestAsset\SimpleClass();

            return $instance;
        });
        $this->SUT->setFactory(
            'SimpleFactoryClassNotFound',
            '\SlidesWorkerTest\ServiceLocator\TestAsset\SimpleFactoryClassNotFound'
        );
        $this->SUT->setInvokable('SimpleInvokable', '\SlidesWorkerTest\ServiceLocator\TestAsset\SimpleClass');
        $this->SUT->setInvokable(
            'SimpleInvokableNotFound',
            '\SlidesWorkerTest\ServiceLocator\TestAsset\SimpleInvokableNotFound'
        );
    }

    public function testHas()
    {
        $this->assertTrue($this->SUT->has('SimpleInvokable'));
        $this->assertTrue($this->SUT->has('SimpleFactoryClass'));
        $this->assertTrue($this->SUT->has('SimpleFactoryCallback'));
        $this->assertFalse($this->SUT->has('NotExists'));

        $this->assertFalse($this->SUT->has('SimpleInvokable2'));

        $testClass = new TestAsset\SimpleClass();
        $this->SUT->set('SimpleInvokable2', $testClass);

        $this->assertTrue($this->SUT->has('SimpleInvokable2'));
    }

    public function testSet()
    {
        $testClass = new TestAsset\SimpleClass();

        $this->SUT->set('SimpleDirectly', $testClass);

        $this->assertTrue($this->SUT->has('SimpleDirectly'));
        $this->assertSame($testClass, $this->SUT->get('SimpleDirectly'));
    }

    public function testSetAllreadyExists()
    {
        $testClass1 = new TestAsset\SimpleClass();
        $testClass2 = new TestAsset\SimpleClass();

        $this->assertFalse($this->SUT->has('SimpleDirectly'));

        $this->SUT->set('SimpleDirectly', $testClass1);
        $this->assertTrue($this->SUT->has('SimpleDirectly'));

        try {
            $this->SUT->set('SimpleDirectly', $testClass2);

            $this->fail('no exeption throw');
        } catch (\SlidesWorker\ServiceLocator\Exception\InvalidServiceNameException $e) {
            $this->assertSame($testClass1, $this->SUT->get('SimpleDirectly'));
            $this->assertNotSame($testClass2, $this->SUT->get('SimpleDirectly'));
        } catch (\Exception $e) {

            $this->fail(sprintf(
                "wrong excception was been throwed\n - given: %s\n - expected: %s",
                get_class($e),
                "\SlidesWorker\ServiceLocator\Exception\InvalidServiceNameException"
            ));
        }
    }

    /**
     * @dataProvider dataProviderTestSetFactory
     */
    public function testSetFactory($name, $factory, $exception)
    {
        if ($exception !== null) {

            $this->setExpectedException($exception);
        }

        $this->SUT->setFactory($name, $factory);
    }

    public function testCreateFromFactoryByInstance()
    {
        $SUT = $this->SUT;
        $methode = new \ReflectionMethod($SUT, 'createFromFactory');
        $methode->setAccessible(true);

        $name= 'SimpleFactoryInstance';

        $return = $methode->invoke($SUT, $SUT->canonicalizeName($name), $name);
        $this->assertInternalType("object", $return);
    }

    public function testCreateFromFactoryByClass()
    {
        $SUT = $this->SUT;
        $methode = new \ReflectionMethod($SUT, 'createFromFactory');
        $methode->setAccessible(true);

        $name= 'SimpleFactoryClass';

        $return = $methode->invoke($SUT, $SUT->canonicalizeName($name), $name);
        $this->assertInternalType("object", $return);
    }

    public function testCreateFromFactoryByCallback()
    {
        $SUT = $this->SUT;
        $methode = new \ReflectionMethod($SUT, 'createFromFactory');
        $methode->setAccessible(true);

        $name= 'SimpleFactoryCallback';

        $return = $methode->invoke($SUT, $SUT->canonicalizeName($name), $name);
        $this->assertInternalType("object", $return);
    }

    /**
     * @expectedException \SlidesWorker\ServiceLocator\Exception\ServiceNotCreatedException
     */
    public function testCreateFromFactoryByInvalidClass()
    {
        $SUT = $this->SUT;
        $methode = new \ReflectionMethod($SUT, 'createFromFactory');
        $methode->setAccessible(true);

        $name= 'SimpleFactoryClassNotFound';

        $return = $methode->invoke($SUT, $SUT->canonicalizeName($name), $name);
    }

    public function testCreateFromFactoryNotExists()
    {
        $SUT = $this->SUT;
        $methode = new \ReflectionMethod($SUT, 'createFromFactory');
        $methode->setAccessible(true);

        $name= 'SimpleFactoryNotExists';

        $return = $methode->invoke($SUT, $SUT->canonicalizeName($name), $name);
        $this->assertNull($return);
    }


    public function testCreateFromInvokable()
    {
        $SUT = $this->SUT;
        $methode = new \ReflectionMethod($SUT, 'createFromInvokable');
        $methode->setAccessible(true);

        $name= 'SimpleInvokable';

        $return = $methode->invoke($SUT, $SUT->canonicalizeName($name), $name);
        $this->assertInternalType("object", $return);
    }

    /**
     * @expectedException \SlidesWorker\ServiceLocator\Exception\ServiceNotCreatedException
     */
    public function testCreateFromInvokableByInvalidClass()
    {
        $SUT = $this->SUT;
        $methode = new \ReflectionMethod($SUT, 'createFromInvokable');
        $methode->setAccessible(true);

        $name= 'SimpleInvokableNotFound';

        $return = $methode->invoke($SUT, $SUT->canonicalizeName($name), $name);
    }

    public function testCreateFromInvokableNotExists()
    {
        $SUT = $this->SUT;
        $methode = new \ReflectionMethod($SUT, 'createFromInvokable');
        $methode->setAccessible(true);

        $name= 'SimpleInvokableNotExists';

        $return = $methode->invoke($SUT, $SUT->canonicalizeName($name), $name);
        $this->assertNull($return);
    }

    /**
     * @expectedException \SlidesWorker\ServiceLocator\Exception\ServiceNotCreatedException
     */
    public function testCreateFail()
    {
        $SUT = $this->SUT;
        $methode = new \ReflectionMethod($SUT, 'create');
        $methode->setAccessible(true);

        $name = "instanceTypeNotExists";

        $return = $methode->invoke($SUT, $SUT->canonicalizeName($name), $name);
    }

    /**
     * @dataProvider dataProviderTestCreateFromInvokableNotExists
     */
    public function testCreate($name)
    {
        $SUT = $this->SUT;
        $methode = new \ReflectionMethod($SUT, 'create');
        $methode->setAccessible(true);

        $return = $methode->invoke($SUT, $SUT->canonicalizeName($name), $name);
        $this->assertInternalType("object", $return);

        if ($return instanceof ServiceLocatorAwareInterface) {
            $this->assertSame($this->SUT, $return->getServiceLocator());
        }
    }

    /**
     * @expectedException \SlidesWorker\ServiceLocator\Exception\ServiceNotCreatedException
     */
    public function testGetNotExists()
    {
        $this->SUT->get('NotExists');
    }

    public function testGet()
    {
        $return = $this->SUT->get('SimpleFactoryInstance');
        $this->assertInternalType("object", $return);

//         $this->assertSame($return, $this->SUT->get('SimpleFactoryInstance'));
    }



    public function dataProviderTestCreateFromInvokableNotExists()
    {
        return array(
            array('SimpleFactoryInstance'),
            array('SimpleFactoryCallback'),
            array('SimpleInvokable'),
        );
    }

    public function dataProviderTestSetFactory()
    {
        return array(
            array(
                'SimpleFactoryClass2',
                '\SlidesWorkerTest\ServiceLocator\TestAsset\SimpleFactoryClass',
                null
            ),

            array(
                'SimpleFactoryClass3',
                new \SlidesWorkerTest\ServiceLocator\TestAsset\SimpleFactoryClass(),
                null
            ),

            array(
                'SimpleFactoryCallback2',
                function (ServiceLocator $locator, $cName, $rName) {
                    $instance = new TestAsset\SimpleClass();
                    return $instance;
                },
                null
            ),

            array(
                'SimpleFactoryClass4',
                new \stdClass(),
                '\SlidesWorker\ServiceLocator\Exception\InvalidArgumentException'
            ),

            array(
                'SimpleFactoryClass5',
                array('foobar'),
                '\SlidesWorker\ServiceLocator\Exception\InvalidArgumentException'
            ),

            array(
                'SimpleFactoryCallbackFunction',
                'SimpleFactoryCallbackFunction',
                null
            ),

            array(
                'SimpleFactoryClass',
                '\SlidesWorkerTest\ServiceLocator\TestAsset\SimpleFactoryClass',
                '\SlidesWorker\ServiceLocator\Exception\InvalidServiceNameException'
            )
        );
    }
}
