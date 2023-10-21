<?php

namespace Task;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface {
    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig() {
        return [
            'factories' => [
                Model\TaskTable::class => function ($container) {
                    $tableGateway = $container->get(Model\TaskTableGateway::class);
                    return new Model\TaskTable($tableGateway);
                },
                Model\TaskTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Task());
                    return new TableGateway('tasks', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }

    public function getControllerConfig() {
        return [
            'factories' => [
                Controller\TaskController::class => function ($container) {
                    return new Controller\TaskController(
                        $container->get(Model\TaskTable::class)
                    );
                },
            ],
        ];
    }
}
