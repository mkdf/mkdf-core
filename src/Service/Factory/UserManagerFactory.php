<?php

namespace MKDF\Core\Service\Factory;

use MKDF\Core\Repository\MKDFCoreRepositoryInterface;
use Interop\Container\ContainerInterface;
use MKDF\Core\Service\UserManager;
/**
 * This is the factory class for UserManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class UserManagerFactory
{
    /**
     * This method creates the UserManager service and returns its instance.
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        //$entityManager = $container->get('doctrine.entitymanager.orm_default');
        $viewRenderer = $container->get('ViewRenderer');
        $config = $container->get('Config');
        $repository = $container->get(MKDFCoreRepositoryInterface::class);

        return new UserManager($repository, $viewRenderer, $config);
    }
}