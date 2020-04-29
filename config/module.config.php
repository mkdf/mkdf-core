<?php

namespace MKDF\Core;

use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

return [
    'controllers' => [
        'factories' => [
            Controller\AuthController::class => Controller\Factory\AuthControllerFactory::class,
            Controller\UserController::class => Controller\Factory\UserControllerFactory::class,
            Controller\MyAccountController::class => Controller\Factory\MyAccountControllerFactory::class
        ],
    ],
    'service_manager' => [
        'aliases' => [
            Repository\MKDFCoreRepositoryInterface::class => Repository\MKDFCoreRepository::class,
            Service\AccountFeatureManagerInterface::class => Service\AccountFeatureManager::class
        ],
        'factories' => [
            Repository\MKDFCoreRepository::class => Repository\Factory\MKDFCoreRepositoryFactory::class,
            \Zend\Authentication\AuthenticationService::class => Service\Factory\AuthenticationServiceFactory::class,
            Service\AuthAdapter::class => Service\Factory\AuthAdapterFactory::class,
            Service\AuthManager::class => Service\Factory\AuthManagerFactory::class,
            Service\UserManager::class => Service\Factory\UserManagerFactory::class,
            Service\AccountFeatureManager::class => Service\Factory\AccountFeatureManagerFactory::class,
            AccountFeature\OverviewFeature::class => InvokableFactory::class,
        ]
    ],
    'view_helpers' => [
        'aliases' => [
            'isAdmin' => View\Helper\IsAdmin::class,
            'routeAllowed' => View\Helper\RouteAllowed::class,
        ],
        'factories' => [
            View\Helper\IsAdmin::class => View\Helper\Factory\IsAdminFactory::class,
            View\Helper\RouteAllowed::class => View\Helper\Factory\RouteAllowedFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'login' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/login',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'login',
                    ],
                ],
            ],
            'logout' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/logout',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'logout',
                    ],
                ],
            ],
            'reset-password' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/reset-password',
                    'defaults' => [
                        'controller' => Controller\UserController::class,
                        'action'     => 'resetPassword',
                    ],
                ],
            ],
            'set-password' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/set-password',
                    'defaults' => [
                        'controller' => Controller\UserController::class,
                        'action'     => 'setPassword',
                    ],
                ],
            ],
            'my-account' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/my-account[/:action]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ],
                    'defaults' => [
                        'controller'    => Controller\MyAccountController::class,
                        'action'        => 'overview',
                    ],
                ]
            ],
            'users' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/users[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller'    => Controller\UserController::class,
                        'action'        => 'index',
                    ],
                ]
            ]

        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'User' => __DIR__ . '/../view',
        ],
    ],
    'controller_plugins' => [
            'factories' => [
                Controller\Plugin\CurrentUserPlugin::class => Controller\Plugin\Factory\CurrentUserPluginFactory::class,
                Controller\Plugin\UserIdFromEmailPlugin::class => Controller\Plugin\Factory\UserIdFromEmailPluginFactory::class,
                Controller\Plugin\AccountFeatureManagerPlugin::class => Controller\Plugin\Factory\AccountFeatureManagerPluginFactory::class
            ],
            'aliases' => [
                'currentUser' => Controller\Plugin\CurrentUserPlugin::class,
                'userIdFromEmail' => Controller\Plugin\UserIdFromEmailPlugin::class,
                'accountFeatureManager' => Controller\Plugin\AccountFeatureManagerPlugin::class
            ]
        ],
    // The 'access_filter' key is used by the User module to restrict or permit
    // access to certain controller actions for unauthenticated visitors.
    'access_filter' => [
        'options' => [
            // The access filter can work in 'restrictive' (recommended) or 'permissive'
            // mode. In restrictive mode all controller actions must be explicitly listed
            // under the 'access_filter' config key, and access is denied to any not listed
            // action for users not logged in. In permissive mode, if an action is not listed
            // under the 'access_filter' key, access to it is permitted to anyone (even for
            // users not logged in. Restrictive mode is more secure and recommended.
            'mode' => 'restrictive'
        ],
        'controllers' => [
            //Controller\IndexController::class => [
            //    // Allow anyone to visit "index" and "about" actions
            //    ['actions' => ['index', 'about'], 'allow' => '*'],
            //    // Allow authenticated users to visit "settings" action
            //    ['actions' => ['settings'], 'allow' => '@']
            //],
            //Controller\DatasetController::class => [
            //    ['actions' => ['index'], 'allow' => '*'],
            //    ['actions' => ['details'], 'allow' => '@']
            //],
            Controller\UserController::class => [
                // ['allow' => 'admin'],
                ['actions' => ['resetPassword','message','setPassword'], 'allow' => '*'],
                ['actions' => ['index'], 'allow' => 'admin'],
                ['actions' => ['view'], 'allow' => 'admin'],
                ['actions' => ['edit','add','change-password'], 'allow' => 'admin']
            ],
            Controller\MyAccountController::class => [
                // ['allow' => 'admin'],
                ['actions' => ['overview'], 'allow' => '@'],
            ],
        ]
    ],
    /*
    'navigation' => [
        'default' => [
            [
                'label' => 'Users',
                'route' => 'users'
            ],
            [
                'label' => 'My Account',
                'route' => 'my-account'
            ]
        ],
    ]
    */
];
