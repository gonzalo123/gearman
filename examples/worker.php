<?php

include __DIR__ . '/../vendor/autoload.php';

use G\Gearman\Builder;

$worker = Builder::createWorker();

$worker->on("slow.process", function ($response, \GearmanJob $job) {
    echo "Response: {$response} unique: {$job->unique()}\n";

    sleep(2);

    return $job->unique();
});

$worker->on("fast.process", function ($response, \GearmanJob $job) {
    echo "Response: {$response} unique: {$job->unique()}\n";

    return $job->unique();
});

$worker->on("exception.process", function () {
    throw new \Exception("Something wrong happens");
});

$worker->run();