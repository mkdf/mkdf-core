<?php

namespace MKDF\Core\Controller\Plugin\Factory;

use MKDF\Core\Repository\MKDFCoreRepositoryInterface;
use Interop\Container\ContainerInterface;
use MKDF\Core\Controller\Plugin\UserIdFromEmailPlugin;

class UserIdFromEmailPluginFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $repository = $container->get(MKDFCoreRepositoryInterface::class);
        return new UserIdFromEmailPlugin($repository);
    }
}