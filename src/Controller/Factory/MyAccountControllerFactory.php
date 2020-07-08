<?php

namespace MKDF\Core\Controller\Factory;

use MKDF\Core\Controller\MyAccountController;
use MKDF\Core\Repository\MKDFCoreRepositoryInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
/**
 * This is the factory for MyAccountController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class MyAccountControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repository = $container->get(MKDFCoreRepositoryInterface::class);


        // Instantiate the controller and inject dependencies
        return new MyAccountController($repository);
    }
}