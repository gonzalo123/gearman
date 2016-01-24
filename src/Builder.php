<?php

namespace G\Gearman;

class Builder
{
    static function createWorker($servers=null)
    {
        $worker = new \GearmanWorker();
        $worker->addServers($servers);

        return new Worker($worker);
    }

    static function createClient($servers=null)
    {
        $client = new \GearmanClient();
        $client->addServers($servers);

        return new Client($client);
    }

    static function createTasks($servers=null)
    {
        $client = new \GearmanClient();
        $client->addServers($servers);

        return new Tasks($client);
    }
}