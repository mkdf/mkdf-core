<?php

namespace MKDF\Core\Controller;

use MKDF\Datasets\Repository\MKDFDatasetRepositoryInterface;
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
    public function __construct(MKDFCoreRepositoryInterface $repository, MKDFDatasetRepositoryInterface $datasetRepository)
    {
        $this->_repository = $repository;
        $this->_datasetRepository = $datasetRepository;
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

    public function datasetsAction () {
        $user = $this->currentUser();
        //anonymous/logged-out user will return an ID of -1
        $userId = $user->getId();
        $actions = [];

        if ($userId > 0) {
            $actions = [
                'label' => 'Actions',
                'class' => '',
                'buttons' => [[ 'type' => 'primary', 'label' => 'Create a new dataset', 'icon' => 'create', 'target' => 'dataset', 'params' => ['action' => 'add']]]
            ];
        }

        $userDatasets = $this->_datasetRepository->findUserDatasets($userId);

        $paginator = new Paginator(new Adapter\ArrayAdapter($userDatasets));
        $page = $this->params()->fromQuery('page', 1);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(10);

        return new ViewModel([
            'message' => 'Datasets ',
            //'datasets' => $this->datasetCollectionToArray($datasetCollection),
            'datasets' => $paginator,
            'user' => $user,
            'userid' => $userId,
            'actions' => $actions,
            'features' => $this->accountFeatureManager()->getFeatures($userId),
        ]);
    }
}