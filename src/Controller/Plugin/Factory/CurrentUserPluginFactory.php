<?php

namespace MKDF\Core\Controller\Plugin\Factory;

use MKDF\Core\Service\AuthManager;
use Interop\Container\ContainerInterface;
use MKDF\Core\Controller\Plugin\CurrentUserPlugin;
class CurrentUserPluginFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $authManager = $container->get(AuthManager::class);

        return new CurrentUserPlugin($authManager);
    }
}