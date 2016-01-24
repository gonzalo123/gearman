<?php

use G\Gearman\Tasks;

class TasksTest extends \PHPUnit_Framework_TestCase
{
    private $actions = ['addTask', 'addTaskHigh', 'addTaskLow', 'addTaskBackground', 'addTaskHighBackground', 'addTaskLowBackground'];

    public function test_simple_task()
    {
        foreach ($this->actions as $action) {
            $gearmanClient = $this->getMock('GearmanClient');

            $gearmanClient
                ->expects($this->any())
                ->method('returnCode')
                ->willReturn(\GEARMAN_SUCCESS);

            $gearmanClient
                ->expects($this->exactly(3))
                ->method($action)
                ->willReturnCallback(function ($name, $workload, $context, $unique) {
                    switch ($name) {
                        case 'hello1':
                            $this->assertEquals(json_encode("workload1"), $workload);
                            $this->assertEquals(['CONTEXT1'], $context);
                            $this->assertEquals('uid1', $unique);
                            break;
                        case 'hello2':
                            $this->assertEquals(json_encode([1, 2, 3]), $workload);
                            $this->assertEquals('CONTEXT', $context);
                            $this->assertEquals('uid2', $unique);
                            break;
                        case 'hello3':
                            $this->assertEquals(json_encode("workload3"), $workload);
                            $this->assertEquals(function () {}, $context);
                            $this->assertEquals('uid3', $unique);
                            break;
                        default:
                            $this->assertTrue(false, "This code should be never reached");
                    }
                });

            $tasks = new Tasks($gearmanClient);

            $tasks->onSuccess(function (\GearmanTask $task, $context) {
                $out = is_callable($context) ? $context($task) : $task->data();
                echo "onSuccess response: " . $out . " id: {$task->unique()}\n";
            });

            $tasks->$action('hello1', "workload1", ['CONTEXT1'], 'uid1');
            $tasks->$action('hello2', [1, 2, 3], 'CONTEXT', 'uid2');
            $tasks->$action('hello3', "workload3", function () {}, 'uid3');
            $tasks->runTasks();
        }
    }
}