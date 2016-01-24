<?php

use G\Gearman\Builder;

class BuilderTest extends \PHPUnit_Framework_TestCase
{
    public function test_worker()
    {
        $worker = Builder::createWorker();
        $this->assertInstanceOf('G\Gearman\Worker', $worker);
    }

    public function test_Client()
    {
        $client = Builder::createClient();
        $this->assertInstanceOf('G\Gearman\Client', $client);
    }

    public function test_Tasks()
    {
        $tasks = Builder::createTasks();
        $this->assertInstanceOf('G\Gearman\Tasks', $tasks);
    }
}