<?php

use G\Gearman\Tasks;

class TasksTest extends \PHPUnit_Framework_TestCase
{
    private $actions = ['addTask', 'addTaskHigh', 'addTaskLow', 'addTaskBackground', 'addTaskHighBackground', 'addTaskLowBackground'];

    public function test_simple_tasks()
    {
        foreach ($this->actions as $action) {
            $callCount = 0;
            $methods= $this->actions;
            $methods[] = 'data';
            $methods[] = 'unique';

            $gearmanClient = $this->getMockBuilder('GearmanClient')
                                  ->setMethods($methods)
                                  ->getMock();

            $gearmanClient
                ->expects($this->exactly(1))
                ->method('setCompleteCallback')
                ->willReturnCallback(function (callable $callback)  {
                    $task = $this->getMock('\GearmanTask');
                    $task->expects($this->any())
                        ->method('data')
                        ->willReturn("RESPONSE");

                    $task->expects($this->any())
                         ->method('unique')
                         ->willReturn("uid1");

                    call_user_func($callback, $task, ['CONTEXT']);
                });

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

            $tasks->onSuccess(function (\GearmanTask $task, $context) use (&$callCount){
                $this->assertEquals('RESPONSE', $task->data());
                $this->assertEquals('uid1', $task->unique());
                $this->assertEquals(['CONTEXT'], $context);
                $callCount++;
            });

            $tasks->$action('hello1', "workload1", ['CONTEXT1'], 'uid1');
            $tasks->$action('hello2', [1, 2, 3], 'CONTEXT', 'uid2');
            $tasks->$action('hello3', "workload3", function () {}, 'uid3');
            $tasks->runTasks();
            $this->assertEquals(1, $callCount);
        }
    }
}