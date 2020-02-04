<?php


namespace MKDF\Core\Repository\Factory;

use MKDF\Core\Repository\MKDFCoreRepository;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class MKDFCoreRepositoryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get("Config");
        return new MKDFCoreRepository($config);
    }
}