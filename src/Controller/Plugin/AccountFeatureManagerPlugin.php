<?php


namespace MKDF\Core\Controller\Plugin;

use MKDF\Core\Service\AccountFeatureManagerInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class AccountFeatureManagerPlugin extends AbstractPlugin
{
    private $_manager;

    public function __construct(AccountFeatureManagerInterface $manager)
    {
        $this->_manager = $manager;
    }

    public function __invoke()
    {
        return $this->_manager;
    }
}