<?php

namespace MKDF\Core\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use MKDF\Core\Entity\User;
use MKDF\Core\Service\UserManager;
use MKDF\Core\Form\UserForm;
use MKDF\Core\Form\PasswordChangeForm;
use MKDF\Core\Form\PasswordResetForm;
use MKDF\Core\Repository\MKDFCoreRepositoryInterface;
use Zend\Paginator\Adapter;
use Zend\Paginator\Paginator;

class MyAccountController extends AbstractActionController
{
    private $_repository;

    /**
     * Constructor.
     */
    public function __construct(MKDFCoreRepositoryInterface $repository)
    {
        $this->_repository = $repository;
    }

    public function overviewAction () {
        $user = $this->currentUser();
        $userId = $user->getId();
        return new ViewModel([
            'user' => $user,
            'userid' => $userId,
            'features' => $this->accountFeatureManager()->getFeatures($userId),
        ]);

    }

}