<?php
include __DIR__ . '/../vendor/autoload.php';

use G\Gearman\Builder;

$client = Builder::createClient();

$client->onSuccess(function ($response) {
    echo $response;
});

$client->doNormal('fast.process', "Hello");