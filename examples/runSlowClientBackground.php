<?php
include __DIR__ . '/../vendor/autoload.php';

use G\Gearman\Builder;

$client = Builder::createClient();

$client->doBackground('slow.process', "Hello1");
$client->doBackground('slow.process', "Hello2");
$client->doBackground('slow.process', "Hello3");