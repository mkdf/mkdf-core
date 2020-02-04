<?php

namespace MKDF\Core\Controller\Factory;

use Interop\Container\ContainerInterface;
use MKDF\Core\Controller\AuthController;
use Zend\ServiceManager\Factory\FactoryInterface;
use MKDF\Core\Service\AuthManager;
use MKDF\Core\Service\UserManager;
/**
 * This is the factory for AuthController. Its purpose is to instantiate the controller
 * and inject dependencies into its constructor.
 */
class AuthControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $authManager = $container->get(AuthManager::class);
        $userManager = $container->get(UserManager::class);
        return new AuthController($authManager, $userManager);
    }
}