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

/**
 * This controller is responsible for user management (adding, editing,
 * viewing users and changing user's password).
 */
class UserController extends AbstractActionController
{
    private $_repository;
    private $config;
    private $_userManager;

    /**
     * Constructor.
     */
    public function __construct(MKDFCoreRepositoryInterface $repository, UserManager $userManager)
    {
        //$this->entityManager = $entityManager;
        $this->_repository = $repository;
        $this->_userManager = $userManager;
        //$this->config = $config;
    }

    private function userCollectionToArray($userCollection) {
        $result = [];
        foreach ($userCollection as $user) {
            array_push($result, $user->getProperties());
        }
        return $result;
    }

    /**
     * This is the default "index" action of the controller. It displays the
     * list of users.
     */
    public function indexAction()
    {
        $userCollection = $this->_repository->findAllUsers();

        $paginator = new Paginator(new Adapter\ArrayAdapter($userCollection));
        $page = $this->params()->fromQuery('page', 1);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(10);

        return new ViewModel([
            //'users' => $userCollection,
            'users' => $paginator,
            'actions' => [
                'label' => 'Actions',
                'class' => '',
                'buttons' => [[ 'type' => 'primary', 'label' => 'Create a new user', 'icon' => 'create', 'target' => 'users', 'params' => ['action' => 'add']]]
            ]
        ]);
    }

    /**
     * This action displays a page allowing to add a new user.
     */
    public function addAction()
    {
        // Create user form
        $form = new UserForm('create', $this->_repository);

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();

            $form->setData($data);

            // Validate form
            if($form->isValid()) {

                // Get filtered and validated data
                $data = $form->getData();

                // Add user.
                $user = $this->_userManager->addUser($data);

                // Redirect to "view" page
                return $this->redirect()->toRoute('users',
                    ['action'=>'index']);
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }

    /**
     * The "view" action displays a page allowing to view user's details.
     */
    public function viewAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Find a user with such ID.
        /*
        $user = $this->entityManager->getRepository(User::class)
            ->find($id);
        */
        $user = $this->_repository->findUser($id);
        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        return new ViewModel([
            'user' => $user,
            'actions' => [
                'label' => 'Actions',
                'class' => '',
                'buttons' => [
                    ['type'=>'warning','label'=>'Edit', 'icon'=>'edit', 'target'=> 'users', 'params'=> ['id' => $id, 'action' => 'edit']],
                    ['type'=>'warning','label'=>'Change password', 'icon'=>'key', 'target'=> 'users', 'params'=> ['id' => $id, 'action' => 'change-password']],
                    ['type'=>'danger','label'=>'Delete', 'icon'=>'delete', 'target'=> 'users', 'params'=> ['id' => $id, 'action' => 'delete-confirm']]
                ]
            ]
        ]);
    }

    /**
     * The "edit" action displays a page allowing to edit user.
     */
    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $user = $this->_repository->findUser($id);

        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Create user form
        $form = new UserForm('update', $this->_repository, $user);

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();

            $form->setData($data);

            // Validate form
            if($form->isValid()) {

                // Get filtered and validated data
                $data = $form->getData();

                // Update the user.
                $this->_userManager->updateUser($user, $data);

                // Redirect to "view" page
                return $this->redirect()->toRoute('users',
                    ['action'=>'view', 'id'=>$user->getId()]);
            }
        } else {
            $form->setData(array(
                'full_name'=>$user->getFullName(),
                'email'=>$user->getEmail(),
                'status'=>$user->getStatus(),
                'admin'=>$user->getIsAdmin()
            ));
        }

        return new ViewModel(array(
            'user' => $user,
            'form' => $form
        ));
    }

    /**
     * This action displays a page allowing to change user's password.
     */
    public function changePasswordAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $user = $this->_repository->findUser($id);

        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Create "change password" form
        $form = new PasswordChangeForm('change');

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();

            $form->setData($data);

            // Validate form
            if($form->isValid()) {

                // Get filtered and validated data
                $data = $form->getData();

                // Try to change password.
                if (!$this->_userManager->changePassword($user, $data)) {
                    $this->flashMessenger()->addErrorMessage(
                        'Sorry, the old password is incorrect. Could not set the new password.');
                } else {
                    $this->flashMessenger()->addSuccessMessage(
                        'Changed the password successfully.');
                }

                // Redirect to "view" page
                return $this->redirect()->toRoute('users',
                    ['action'=>'view', 'id'=>$user->getId()]);
            }
        }

        return new ViewModel([
            'user' => $user,
            'form' => $form
        ]);
    }

    /**
     * This action displays the "Reset Password" page.
     */
    public function resetPasswordAction()
    {
        // Create form
        $form = new PasswordResetForm();

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();

            $form->setData($data);

            // Validate form
            if($form->isValid()) {

                // Look for the user with such email.
                $user = $this->_repository->findUserByEmail($data['email']);

                if ($user!=null && $user->getStatus() == User::STATUS_ACTIVE) {
                    // Generate a new password for user and send an E-mail
                    // notification about that.
                    $this->_userManager->generatePasswordResetToken($user);

                    // Redirect to "message" page
                    return $this->redirect()->toRoute('users',
                        ['action'=>'message', 'id'=>'sent']);
                } else {
                    return $this->redirect()->toRoute('users',
                        ['action'=>'message', 'id'=>'invalid-email']);
                }
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }

    /**
     * This action displays an informational message page.
     * For example "Your password has been reset" and so on.
     */
    public function messageAction()
    {
        // Get message ID from route.
        $id = (string)$this->params()->fromRoute('id');

        // Validate input argument.
        if($id!='invalid-email' && $id!='sent' && $id!='set' && $id!='failed') {
            throw new \Exception('Invalid message ID specified');
        }

        return new ViewModel([
            'id' => $id
        ]);
    }

    /**
     * This action displays the "Reset Password" page.
     */
    public function setPasswordAction()
    {
        $email = $this->params()->fromQuery('email', null);
        $token = $this->params()->fromQuery('token', null);

        // Validate token length
        if ($token!=null && (!is_string($token) || strlen($token)!=32)) {
            throw new \Exception('Invalid token type or length');
        }

        if($token===null ||
            !$this->_userManager->validatePasswordResetToken($email, $token)) {
            return $this->redirect()->toRoute('users',
                ['action'=>'message', 'id'=>'failed']);
        }

        // Create form
        $form = new PasswordChangeForm('reset');

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();

            $form->setData($data);

            // Validate form
            if($form->isValid()) {

                $data = $form->getData();

                // Set new password for the user.
                if ($this->_userManager->setNewPasswordByToken($email, $token, $data['new_password'])) {

                    // Redirect to "message" page
                    return $this->redirect()->toRoute('users',
                        ['action'=>'message', 'id'=>'set']);
                } else {
                    // Redirect to "message" page
                    return $this->redirect()->toRoute('users',
                        ['action'=>'message', 'id'=>'failed']);
                }
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }
}