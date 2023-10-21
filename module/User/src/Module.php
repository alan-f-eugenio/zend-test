<?php

namespace User;

use User\Controller\AuthController;
use User\Service\AuthManager;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;

class Module implements ConfigProviderInterface {
    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig() {
        return [
            'factories' => [
                Model\UserTable::class => function ($container) {
                    $tableGateway = $container->get(Model\UserTableGateway::class);
                    return new Model\UserTable($tableGateway);
                },
                Model\UserTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\User());
                    return new TableGateway('users', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }

    public function getControllerConfig() {
        return [
            'factories' => [
                Controller\AuthController::class => function ($container) {
                    return new Controller\AuthController(
                        $container->get(Model\UserTable::class)
                    );
                },
            ],
        ];
    }


    public function onBootstrap(MvcEvent $event) {
        $eventManager = $event->getApplication()->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();
        $sharedEventManager->attach(
            AbstractActionController::class,
            MvcEvent::EVENT_DISPATCH,
            [$this, 'onDispatch'],
            100
        );

        $sessionManager = $event->getApplication()->getServiceManager()->get('Zend\Session\SessionManager');

        $this->forgetInvalidSession($sessionManager);
    }

    protected function forgetInvalidSession($sessionManager) {
        try {
            $sessionManager->start();
            return;
        } catch (\Exception $e) {
        }
        session_unset();
    }

    public function onDispatch(MvcEvent $event) {
        $controller = $event->getTarget();
        $controllerName = $event->getRouteMatch()->getParam('controller', null);
        $actionName = $event->getRouteMatch()->getParam('action', null);

        $actionName = str_replace('-', '', lcfirst(ucwords($actionName, '-')));

        $authManager = $event->getApplication()->getServiceManager()->get(AuthManager::class);

        if (
            $controllerName != AuthController::class &&
            !$authManager->filterAccess($controllerName, $actionName)
        ) {

            $uri = $event->getApplication()->getRequest()->getUri();
            $uri->setScheme(null)
                ->setHost(null)
                ->setPort(null)
                ->setUserInfo(null);

            return $controller->redirect()->toRoute(
                'login'
            );
        }
    }
}
