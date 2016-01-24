<?php

namespace G\Gearman;

class Tasks
{
    private $client;
    private $tasks;

    public function __construct(\GearmanClient $client)
    {
        $this->tasks = [];
        $this->client = $client;
    }

    public function addTask($name, $workload=null, $context = null, $unique = null)
    {
        $this->tasks[] = [__FUNCTION__, $name, $workload, $context, $unique];
    }

    public function addTaskHigh($name, $workload=null, $context = null, $unique = null)
    {
        $this->tasks[] = [__FUNCTION__, $name, $workload, $context, $unique];
    }

    public function addTaskLow($name, $workload=null, $context = null, $unique = null)
    {
        $this->tasks[] = [__FUNCTION__, $name, $workload, $context, $unique];
    }

    public function addTaskBackground($name, $workload=null, $context = null, $unique = null)
    {
        $this->tasks[] = [__FUNCTION__, $name, $workload, $context, $unique];
    }

    public function addTaskHighBackground($name, $workload=null, $context = null, $unique = null)
    {
        $this->tasks[] = [__FUNCTION__, $name, $workload, $context, $unique];
    }

    public function addTaskLowBackground($name, $workload=null, $context = null, $unique = null)
    {
        $this->tasks[] = [__FUNCTION__, $name, $workload, $context, $unique];
    }

    public function runTasks()
    {
        foreach ($this->tasks as list($actionName, $name, $workload, $context, $unique)) {
            $this->client->$actionName($name, json_encode($workload), $context, $unique);
        }

        $this->client->runTasks();
    }

    public function onSuccess(callable $callback)
    {
        $this->client->setCompleteCallback($callback);
    }

    public function onException(callable $callback)
    {
        $this->client->setExceptionCallback($callback);
    }

    public function onFail(callable $callback)
    {
        $this->client->setFailCallback($callback);
    }
}