<?php

namespace MKDF\Core\Controller\Plugin;

use MKDF\Core\Repository\MKDFCoreRepository;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use MKDF\Core\Entity\User;
/**
 * This controller plugin is designed to let you get the id of a user from their email address
 */
class UserIdFromEmailPlugin extends AbstractPlugin
{
    private $_repository;

    /**
     * Constructor.
     * @param MKDFCoreRepository $repository
     */
    public function __construct(MKDFCoreRepository $repository)
    {
        $this->_repository = $repository;
    }
    /**
     * This method is called when you invoke this plugin in your controller: $userId = $this->currentUser($email);
     * @param string $email
     * @return int|null
     */
    public function __invoke($email)
    {
        $user = new User();
        $user = $this->_repository->findUserByEmail($email);
        if ($user == null) {
            return 0;
        }
        return $user->getId();
    }
}