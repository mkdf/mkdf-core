<?php
namespace MKDF\Core\View\Helper;

use Zend\View\Helper\AbstractHelper;
use MKDF\Core\Service\AuthManager;
use MKDF\Core\Service\UserManager;

class IsAdmin extends AbstractHelper {
    private $_authManager;
    
    public function __construct(AuthManager $am){
        $this->_authManager = $am;
    }
    
    public function __invoke() {
        return $this->_authManager->getCurrentUser()->getIsAdmin();
    }
}