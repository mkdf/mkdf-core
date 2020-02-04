<?php
namespace MKDF\Core\Service;
use Zend\Authentication\Result;
use Zend\Session\SessionManager;
use Zend\Authentication\AuthenticationService;
use MKDF\Core\Repository\MKDFCoreRepositoryInterface;
use MKDF\Core\Entity\User;
use Zend\Router\RouteStackInterface;
/**
 * The AuthManager service is responsible for user's login/logout and simple access
 * filtering. The access filtering feature checks whether the current visitor
 * is allowed to see the given page or not.
 */
class AuthManager
{
    /**
     * Authentication service.
     * @var \Zend\Authentication\AuthenticationService
     */
    private $authService;

    /**
     * Session manager.
     * @var Zend\Session\SessionManager
     */
    private $sessionManager;

    /**
     * @var 
     */
    private $repository;
    
    /**
     * Contents of the 'access_filter' config key.
     * @var array
     */
    private $config;

    private $_user;
    /**
     * Constructs the service.
     */
    public function __construct(AuthenticationService $authService, SessionManager $sessionManager, MKDFCoreRepositoryInterface $repository, RouteStackInterface $router, $config)
    {
        $this->authService = $authService;
        $this->sessionManager = $sessionManager;
        $this->repository = $repository;
        $this->config = $config;
        $this->router = $router;
    }

    /**
     * Performs a login attempt. If $rememberMe argument is true, it forces the session
     * to last for one month (otherwise the session expires on one hour).
     */
    public function login($email, $password, $rememberMe)
    {
        // Check if user has already logged in. If so, do not allow to log in
        // twice.
        if ($this->authService->getIdentity()!=null) {
            throw new \Exception('Already logged in');
        }

        // Authenticate with login/password.
        $authAdapter = $this->authService->getAdapter();
        $authAdapter->setEmail($email);
        $authAdapter->setPassword($password);
        $result = $this->authService->authenticate();
        // If user wants to "remember him", we will make session to expire in
        // one month. By default session expires in 1 hour (as specified in our
        // config/global.php file).
        if ($result->getCode()==Result::SUCCESS && $rememberMe) {
            // Session cookie will expire in 1 month (30 days).
            $this->sessionManager->rememberMe(60*60*24*30);
        }

        return $result;
    }

    /**
     * Performs user logout.
     */
    public function logout()
    {
        // Allow to log out only when user is logged in.
        if ($this->authService->getIdentity()==null) {
            throw new \Exception('The user is not logged in');
        }

        // Remove identity from session.
        $this->authService->clearIdentity();
        
        $this->_user = null;
    }

    /**
     * This is a simple access control filter. It is able to restrict unauthorized
     * users to visit certain pages.
     *
     * This method uses the 'access_filter' key in the config file and determines
     * whether the current visitor is allowed to access the given controller action
     * or not. It returns true if allowed; otherwise false.
     */
    public function filterAccess($controllerClass, $actionName)
    {
        // Determine mode - 'restrictive' (default) or 'permissive'. In restrictive
        // mode all controller actions must be explicitly listed under the 'access_filter'
        // config key, and access is denied to any not listed action for unauthorized users.
        // In permissive mode, if an action is not listed under the 'access_filter' key,
        // access to it is permitted to anyone (even for not logged in users.
        // Restrictive mode is more secure and recommended to use.
        $mode = isset($this->config['options']['mode'])?$this->config['options']['mode']:'restrictive';
        if ($mode!='restrictive' && $mode!='permissive')
            throw new \Exception('Invalid access filter mode (expected either restrictive or permissive mode');

        $isAdmin = $this->getCurrentUser()->getIsAdmin();
        if (isset($this->config['controllers'][$controllerClass])) {
            $items = $this->config['controllers'][$controllerClass];
            foreach ($items as $item) {
                $actionList = $item['actions'];
                $allow = $item['allow'];
                if (is_array($actionList) && in_array($actionName, $actionList) || $actionList=='*') {
                    if ($allow=='*'){
                        return true; // Anyone is allowed to see the page.
                    } else if ($allow=='@' && $this->authService->hasIdentity()) {
                        return true; // Only authenticated user is allowed to see the page.
                    }
                    else if ($allow=='admin' && $isAdmin) {
                        return true; // Only admin users are allowed to see the page.
                    }
                    else {
                        return false; // Access denied.
                    }
                }
            }
        }
        // In restrictive mode, we forbid access for unauthorized users to any
        // action not listed under 'access_filter' key (for security reasons).
        if ($mode=='restrictive' && !$this->authService->hasIdentity())
            return false;
        // Permit access to this page.
        return true;
    }
    
    public function routeAllowed($route){
        $r = new \Zend\Http\Request();
        $r->setUri($route);
        $match = $this->router->match($r);
        if(!$match){
            throw new \Exception("Unkwnown route: '" . $route . "'");
        }
        return $this->filterAccess($match->getParam('controller'), $match->getParam('action'));
    }
    
    public function getCurrentUser($useCachedUser = true)
    {
        // If current user is already fetched, return it.
        if ($useCachedUser && $this->_user!==null)
            return $this->_user;

        // Check if user is logged in.
        if ($this->authService->hasIdentity()) {

            // Fetch User entity from database.
            $this->_user = $this->repository->findUserByEmail($this->authService->getIdentity());
            if ($this->_user==null) {
                // Oops.. the identity presents in session, but there is no such user in database.
                // We throw an exception, because this is a possible security problem.
                throw new \Exception('Not found user with such email');
            }

            // Return found User.
            return $this->_user;
        }

        return User::anonymous();
    }
}