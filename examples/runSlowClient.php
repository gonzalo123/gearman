<?php
include __DIR__ . '/../vendor/autoload.php';

use G\Gearman\Builder;

$client = Builder::createClient();

$client->onSuccess(function ($response) {
    echo $response . "\n";
});

$client->doNormal('slow.process', "Hello");