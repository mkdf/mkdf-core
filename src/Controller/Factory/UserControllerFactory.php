<?php

namespace MKDF\Core\Controller\Factory;

use MKDF\Core\Repository\MKDFCoreRepositoryInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use MKDF\Core\Controller\UserController;
use MKDF\Core\Service\UserManager;
/**
 * This is the factory for UserController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class UserControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $userManager = $container->get(UserManager::class);
        $repository = $container->get(MKDFCoreRepositoryInterface::class);

        // Instantiate the controller and inject dependencies
        return new UserController($repository, $userManager);
    }
}