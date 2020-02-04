<?php

namespace MKDF\Core\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use MKDF\Core\Entity\User;
use MKDF\Core\Service\AuthManager;
/**
 * This controller plugin is designed to let you get the currently logged in User entity
 * inside your controller.
 */
class CurrentUserPlugin extends AbstractPlugin
{

    private $authManager;

    /**
     * Constructor.
     */
    public function __construct(AuthManager $authManager)
    {
        $this->authManager = $authManager;
    }
    /**
     * This method is called when you invoke this plugin in your controller: $user = $this->currentUser();
     * @param bool $useCachedUser If true, the User entity is fetched only on the first call (and cached on subsequent calls).
     * @return User|null
     */
    public function __invoke($useCachedUser = true)
    {
        return $this->authManager->getCurrentUser($useCachedUser);
    }
}