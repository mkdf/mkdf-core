<?php
namespace MKDF\Core\View\Helper;

use Zend\View\Helper\AbstractHelper;
use MKDF\Core\Service\AuthManager;

class RouteAllowed extends AbstractHelper {
    private $_authManager;
    
    public function __construct(AuthManager $am){
        $this->_authManager = $am;
    }
    
    public function __invoke($route) {
        return $this->_authManager->routeAllowed($route);
    }
}