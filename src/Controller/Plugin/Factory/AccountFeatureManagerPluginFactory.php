<?php


namespace MKDF\Core\Controller\Plugin\Factory;

use MKDF\Core\Controller\Plugin\AccountFeatureManagerPlugin;
use MKDF\Core\Service\AccountFeatureManagerInterface;
use Interop\Container\ContainerInterface;

class AccountFeatureManagerPluginFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $m = $container->get(AccountFeatureManagerInterface::class);
        return new AccountFeatureManagerPlugin($m);
    }
}