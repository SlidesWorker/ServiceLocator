<?php
namespace SlidesWorker\ServiceLocator\Initializer;

use SlidesWorker\ServiceLocator\Exception\InvalidArgumentException;

trait InitializerAwareTrait
{
    protected $initializers = array();
    protected $initializerAttrLists = array();

    public function addInitializer($initializer, $topOfStack = false)
    {
        if (!($initializer instanceof InitializerInterface || is_callable($initializer))) {
            if (is_string($initializer) && class_exists($initializer)) {
                $initializer = new $initializer;
            }

            if (!($initializer instanceof InitializerInterface || is_callable($initializer))) {
                throw new InvalidArgumentException('$initializer should be callable.');
            }
        }

        if ($topOfStack) {
            array_unshift($this->initializers, $initializer);
        } else {
            array_push($this->initializers, $initializer);
        }

        return $this;
    }

    /**
     *
     * @param object $object
     */
    public function callInitializers($instance)
    {
        foreach ($this->initializers as $initializer) {
            if ($initializer instanceof InitializerInterface) {
                $callable = array($initializer,'initialize');
            } else {
                $callable = $initializer;
            }

            call_user_func($callable, $instance, $this);
        }
    }
}
