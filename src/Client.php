<?php

namespace G\Gearman;

class Client
{
    private $onSuccessCallback;
    private $client;

    public function __construct(\GearmanClient $client)
    {
        $this->client = $client;
    }

    public function setUp()
    {
        if (!defined('GEARMAN_SUCCESS')) {
            define('GEARMAN_SUCCESS', 0);
        }
        if (!defined('GEARMAN_IO_WAIT')) {
            define('GEARMAN_IO_WAIT', 1);
        }
    }
    public function doHigh($name, $workload, $unique = null)
    {
        return $this->doAction(__FUNCTION__, $name, $workload, $unique);
    }

    public function doNormal($name, $workload, $unique = null)
    {
        return $this->doAction(__FUNCTION__, $name, $workload, $unique);
    }

    public function doLow($name, $workload, $unique = null)
    {
        return $this->doAction(__FUNCTION__, $name, $workload, $unique);
    }

    public function doBackground($name, $workload, $unique = null)
    {
        return $this->doAction(__FUNCTION__, $name, $workload, $unique);
    }

    public function doHighBackground($name, $workload, $unique = null)
    {
        return $this->doAction(__FUNCTION__, $name, $workload, $unique);
    }

    public function doLowBackground($name, $workload, $unique = null)
    {
        return $this->doAction(__FUNCTION__, $name, $workload, $unique);
    }

    private function doAction($action, $name, $workload, $unique)
    {
        $handle = $this->client->$action($name, json_encode($workload), $unique);
        $returnCode = $this->client->returnCode();
        if ($returnCode != \GEARMAN_SUCCESS) {
            throw new \Exception($this->client->error(), $returnCode);
        } else {
            if ($this->onSuccessCallback) {
                return call_user_func($this->onSuccessCallback, $handle);
            }
        }

        return null;
    }

    public function onSuccess(callable $callback)
    {
        $this->onSuccessCallback = $callback;
    }
}