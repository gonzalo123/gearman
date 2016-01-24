<?php

namespace G\Gearman;

class Worker
{
    private $worker;

    public function __construct(\GearmanWorker $worker)
    {
        $this->worker = $worker;
    }

    public function on($name, callable $callback, $context = null, $timeout = 0)
    {
        $this->worker->addFunction($name, function (\GearmanJob $job) use ($callback) {
            return call_user_func($callback, json_decode($job->workload()), $job);
        }, $context, $timeout);
    }

    public function run()
    {
        try {
            $this->loop();
        } catch (\Exception $e) {
            echo $e->getMessage() . "\n";
            $this->run();
        }
    }

    private function loop()
    {
        while ($this->worker->work()) {
        }
    }
}