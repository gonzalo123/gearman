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

    public function doHigh($name, $workload=null, $unique = null)
    {
        return $this->doAction(__FUNCTION__, $name, $workload, $unique);
    }

    public function doNormal($name, $workload=null, $unique = null)
    {
        return $this->doAction(__FUNCTION__, $name, $workload, $unique);
    }

    public function doLow($name, $workload=null, $unique = null)
    {
        return $this->doAction(__FUNCTION__, $name, $workload, $unique);
    }

    public function doBackground($name, $workload=null, $unique = null)
    {
        return $this->doAction(__FUNCTION__, $name, $workload, $unique);
    }

    public function doHighBackground($name, $workload=null, $unique = null)
    {
        return $this->doAction(__FUNCTION__, $name, $workload, $unique);
    }

    public function doLowBackground($name, $workload=null, $unique = null)
    {
        return $this->doAction(__FUNCTION__, $name, $workload, $unique);
    }

    private function doAction($action, $name, $workload=null, $unique)
    {
        $workload = (string)$workload;
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