<?php

namespace MKDF\Core\View\Helper\Factory;

use Interop\Container\ContainerInterface;
use MKDF\Core\View\Helper\RouteAllowed;
use Zend\ServiceManager\Factory\FactoryInterface;
use MKDF\Core\Service\AuthManager;
use MKDF\Core\Service\UserManager;
/**
 * This is the factory for AuthController. Its purpose is to instantiate the controller
 * and inject dependencies into its constructor.
 */
class RouteAllowedFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $authManager = $container->get(AuthManager::class);
        return new RouteAllowed($authManager);
    }
}