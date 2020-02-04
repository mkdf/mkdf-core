<?php


namespace MKDF\Core\Service\Factory;

use MKDF\Core\Service\AccountFeatureManager;
use Interop\Container\ContainerInterface;

class AccountFeatureManagerFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new AccountFeatureManager();
    }

}