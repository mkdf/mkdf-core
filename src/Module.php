<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 *
 */

namespace MKDF\Core;

use MKDF\Core\Service\AccountFeatureManagerInterface;
use MKDF\Datasets\DatasetsFeature\PermissionsFeature;
use MKDF\Keys\Feature\AccountKeysFeature;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Controller\AbstractActionController;
use MKDF\Core\Controller\AuthController;
use MKDF\Core\Service\AuthManager;
use MKDF\Core\AccountFeature\OverviewFeature;
use MKDF\Core\AccountFeature\DatasetsFeature;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * This method is called once the MVC bootstrapping is complete and allows
     * to register event listeners.
     */
    public function onBootstrap(MvcEvent $event)
    {
        $featureManager = $event->getApplication()->getServiceManager()->get(AccountFeatureManagerInterface::class);
        $featureManager->registerFeature($event->getApplication()->getServiceManager()->get(OverviewFeature::class));

        // Get event manager
        $eventManager = $event->getApplication()->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();
        // Register the event listener method.
        $sharedEventManager->attach(AbstractActionController::class,
            MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], 100);
        
        $application = $event->getApplication();
        /** @var $serviceManager \Zend\ServiceManager\ServiceManager */
        // $serviceManager = $application->getServiceManager();
        //$pm = $serviceManager->get('ViewHelperManager');
        //$authManager = $serviceManager->get(AuthManager::class);
        //$pm->setInvokableClass('userCanSee', 'mkdf-core\View\Helper\UserCanSee');
    }

    /**
     * Event listener method for the 'Dispatch' event. We listen to the Dispatch
     * event to call the access filter. The access filter allows to determine if
     * the current visitor is allowed to see the page or not. If he/she
     * is not authenticated and is not allowed to see the page, we redirect the user
     * to the login page.
     */
    public function onDispatch(MvcEvent $event)
    {
        // Get controller and action to which the HTTP request was dispatched.
        $controller = $event->getTarget();
        $controllerName = $event->getRouteMatch()->getParam('controller', null);
        $actionName = $event->getRouteMatch()->getParam('action', null);

        // Convert dash-style action name to camel-case.
        $actionName = str_replace('-', '', lcfirst(ucwords($actionName, '-')));

        // Get the instance of AuthManager service.
        $authManager = $event->getApplication()->getServiceManager()->get(AuthManager::class);
        // Execute the access filter on every controller except AuthController
        // (to avoid infinite redirect).
        if ($controllerName!=AuthController::class &&
            !$authManager->filterAccess($controllerName, $actionName)) {

            // Remember the URL of the page the user tried to access. We will
            // redirect the user to that URL after successful login.
            $uri = $event->getApplication()->getRequest()->getUri();
            // Make the URL relative (remove scheme, user info, host name and port)
            // to avoid redirecting to other domain by a malicious user.
            $uri->setScheme(null)
                ->setHost(null)
                ->setPort(null)
                ->setUserInfo(null);
            $redirectUrl = $uri->toString();

            // Redirect the user to the "Login" page.
            //if user is anonymous (not logged in)
            if ($controller->currentUser()->getId() == -1) {
                return $controller->redirect()->toRoute('login', [],
                    ['query'=>['redirectUrl'=>$redirectUrl]]);
            }
            else {
                $controller->getResponse()->setStatusCode(404);
                return;
            }
        }
    }
}
