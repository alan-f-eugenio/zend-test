<?php

namespace TaskTest\Controller;

use Prophecy\Argument;
use Task\Model\TaskTable;
use Task\Controller\TaskController;
use Task\Model\Task;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;


class TaskControllerTest extends AbstractHttpControllerTestCase {
    protected $traceError = true;

    protected $taskTable;

    public function setUp() {
        $configOverrides = [];

        $this->setApplicationConfig(ArrayUtils::merge(
            include __DIR__ . '/../../../../config/application.config.php',
            $configOverrides
        ));

        parent::setUp();

        $this->configureServiceManager($this->getApplicationServiceLocator());
    }

    protected function configureServiceManager(ServiceManager $services) {
        $services->setAllowOverride(true);

        $services->setService('config', $this->updateConfig($services->get('config')));
        $services->setService(TaskTable::class, $this->mockTaskTable()->reveal());

        $services->setAllowOverride(false);
    }

    protected function updateConfig($config) {
        $config['db'] = [];
        return $config;
    }

    protected function mockTaskTable() {
        $this->taskTable = $this->prophesize(TaskTable::class);
        return $this->taskTable;
    }

    public function testIndexActionCanBeAccessed() {
        $this->taskTable->fetchAll()->willReturn([]);

        $this->dispatch('/task');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Task');
        $this->assertControllerName(TaskController::class);
        $this->assertControllerClass('TaskController');
        $this->assertMatchedRouteName('task');
    }

    public function testAddActionRedirectsAfterValidPost() {
        $this->taskTable
            ->saveTask(Argument::type(Task::class))
            ->shouldBeCalled();

        $postData = [
            'title'  => 'Task Test',
            'description' => 'Task Test Description',
            'status' => 'pending',
            'id'     => '',
        ];
        $this->dispatch('/task/add', 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/task');
    }

    public function testEditActionRedirectsAfterValidPost() {
        $this->taskTable->getTask(1)->willReturn(new Task());

        $this->taskTable
            ->saveTask(Argument::type(Task::class))
            ->shouldBeCalled();

        $postData = [
            'title'  => 'Task Test',
            'description' => 'Task Test Description',
            'status' => 'completed',
            'id'     => 1,
        ];
        $this->dispatch('/task/edit/1', 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/task');
    }

    public function testDeleteActionRedirectsAfterValidPost() {
        $this->taskTable->getTask(1)->willReturn(new Task());

        $postData = [
            'del'  => 'yes',
        ];
        $this->dispatch('/task/delete/1', 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/task');
    }
}
