<?php

use G\Gearman\Worker;

class WorkerTest extends \PHPUnit_Framework_TestCase
{
    public function test_simple_worker()
    {
        $callCount = 0;

        $gearmanWorker = $this->getMockBuilder('GearmanWorker')
                              ->setMethods(['addFunction', 'work'])
                              ->getMock();
        $gearmanWorker
            ->expects($this->any())
            ->method('work')
            ->willReturnCallback(function () use (&$callCount) {
                $callCount++;
            });

        $gearmanWorker
            ->expects($this->exactly(1))
            ->method('addFunction')
            ->willReturnCallback(function ($name, callable $callback) {
                $this->assertEquals('hello', $name);

                $job = $this->getMockBuilder('GearmanJob')
                            ->setMethods(['workload'])
                            ->getMock();
                $job->expects($this->any())
                    ->method('workload')
                    ->willReturn(json_encode("myWorkload"));

                $response = call_user_func($callback, $job);
                $this->assertEquals('HELLO myWorkload', $response);

            });

        $worker = new Worker($gearmanWorker);

        $worker->on('hello', function ($response, $job) use (&$callCount) {
            $callCount++;
            $this->assertInstanceOf('\GearmanJob', $job);

            return "HELLO {$response}";
        });

        $worker->run();

        $this->assertEquals(2, $callCount);
    }
}