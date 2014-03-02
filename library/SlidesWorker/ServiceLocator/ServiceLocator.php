<?php

namespace SlidesWorker\ServiceLocator;

use SlidesWorker\ServiceLocator\Initializer\InitializerAwareTrait;
use SlidesWorker\ServiceLocator\Initializer\InitializerAwareInterface;

class ServiceLocator implements ServiceLocatorInterface, InitializerAwareInterface
{
    use InitializerAwareTrait;

    protected $services = array();

    protected $servicesInvokable = array();

    protected $servicesFactory = array();

    protected $canonicalNames = array();

    protected $allowOverride = false;

    /**
     *
     * @var array map of characters to be replaced through strtr
     */
    protected $canonicalNamesReplacements = array(
        '-' => '',
        '_' => '',
        ' ' => '',
        '\\' => '',
        '/' => ''
    );

    public function __construct()
    {
        $this->addInitializer('SlidesWorker\ServiceLocator\Initializer\ServiceLocatorInitializer');
    }

    /**
     *
     * @throws RuntimeException
     */
    public function get($name)
    {
        $cName = $this->canonicalizeName($name);

        if ($this->isCreated($cName)) {
            return $this->services[$cName];
        } elseif (! $this->canCreate($cName)) {
            throw new Exception\ServiceNotCreatedException("can not create the wanted instace");
        }

        $instance = $this->create($cName, $name);

        $this->set($cName, $instance);

        return $instance;
    }

    public function set($name, $instance)
    {
        $cName = $this->canonicalizeName($name);

        if ($this->isCreated($cName)) {
            throw new Exception\InvalidServiceNameException(sprintf(
                'A service by the name "%s" (requested "%s") already create and cannot be overridden, '.
                'please use an alternate name ',
                $cName,
                $name
            ));
        }

        $this->services[$cName] = $instance;
    }

    public function has($name)
    {
        if (is_array($name)) {
            list ($cName, $rName) = $name;
        } else {
            $cName = $this->canonicalizeName($name);
        }
        return $this->isCreated($cName) || $this->canCreate($cName);
    }

    protected function isCreated($cName)
    {
        return isset($this->services[$cName]);
    }

    protected function canCreate($cName)
    {
        return isset($this->servicesInvokable[$cName]) ||
               isset($this->servicesFactory[$cName]) ||
               isset($this->servicesCallback[$cName]);
    }

    /**
     * Create an instance of the requested service
     *
     * @param string $cName
     * @param string $rName
     *
     * @return bool object
     */
    protected function create($cName, $rName)
    {
        $instance = null;

        if (isset($this->servicesFactory[$cName])) {
            $instance = $this->createFromFactory($cName, $rName);
        }

        if ($instance === null && isset($this->servicesInvokable[$cName])) {
            $instance = $this->createFromInvokable($cName, $rName);
        }

        if ($instance === null) {
            throw new Exception\ServiceNotCreatedException(sprintf(
                'No valid instance was found for %s%s',
                $cName,
                ($rName ? '(alias: ' . $rName . ')' : '')
            ));
        }

        $this->callInitializers($instance);

        return $instance;
    }

    /**
     *
     * @param string $cName
     * @param string $rName
     * @return object
     */
    protected function createFromInvokable($cName, $rName)
    {
        if (!isset($this->servicesInvokable[$cName])) {
            return null;
        }
        $class = $this->servicesInvokable[$cName];

        if (!class_exists($class, true)) {
            throw new Exception\ServiceNotCreatedException(sprintf(
                'While attempting to create %s%s an invalid invokable class was registered for this instance type.',
                $cName,
                ($rName ? '(alias: ' . $rName . ')' : '')
            ));
        }
        return new $class();
    }

    /**
     *
     * @param string $cName
     * @param string $rName
     * @throws ServiceNotCreatedException
     * @return object
     */
    protected function createFromFactory($cName, $rName)
    {
        if (! isset($this->servicesFactory[$cName])) {
            return;
        }
        $factory = $this->servicesFactory[$cName];
        if (is_string($factory) && class_exists($factory, true)) {
            $factory = new $factory();
        }

        $attr = array($this, $cName, $rName);

        if ($factory instanceof FactoryInterface) {
            return call_user_func_array(array($factory, 'createService'), $attr);
        } elseif (is_callable($factory)) {
            return call_user_func_array($factory, $attr);
        }

        throw new Exception\ServiceNotCreatedException(sprintf(
            'While attempting to create %s%s an invalid factory was registered for this instance type.',
            $cName,
            ($rName ? '(alias: ' . $rName . ')' : '')
        ));
    }

    /**
     * Canonicalize name
     *
     * @param string $name
     * @return string
     */
    public function canonicalizeName($name)
    {
        if (isset($this->canonicalNames[$name])) {
            return $this->canonicalNames[$name];
        }

        // this is just for performance instead of using str_replace
        return $this->canonicalNames[$name] = strtolower(strtr($name, $this->canonicalNamesReplacements));
    }

    protected function guardServiceNameIsNotExists($cName, $rName)
    {
        if ($this->has(array($cName,$rName))) {
            throw new Exception\InvalidServiceNameException(sprintf(
                'A service by the name "%s" (requested "%s") already exists and cannot be overridden, '.
                'please use an alternate name ',
                $cName,
                $rName
            ));
        }
    }

    protected function guardFactoryIsValid($factory)
    {
        if (is_callable($factory) || is_string($factory)) {
            return;
        }

        if (!is_object($factory)) {
            $given = gettype($factory) . " " . var_export($factory, true);
        } elseif ($factory instanceof FactoryInterface) {
            return;
        } else {
            $given = 'object ' . get_class($factory);
        }

        throw new Exception\InvalidArgumentException(sprintf(
            "Factory must be the class name or a instance of an FactoryInterface or a callable var type.\n given: %s",
            $given
        ));
    }

    public function setFactory($name, $factory)
    {
        $cName = $this->canonicalizeName($name);

        $this->guardFactoryIsValid($factory);
        $this->guardServiceNameIsNotExists($cName, $name);

        $this->servicesFactory[$cName] = $factory;

        return $this;
    }

    public function setInvokable($name, $invokableClassName)
    {
        $cName = $this->canonicalizeName($name);

        $this->guardServiceNameIsNotExists($cName, $name);

        $this->servicesInvokable[$cName] = $invokableClassName;

        return $this;
    }
}
