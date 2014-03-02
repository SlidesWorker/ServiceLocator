<?php
namespace SlidesWorker\ServiceLocator;

interface ServiceLocatorAwareInterface
{
    /**
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator);

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator();
}
