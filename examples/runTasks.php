<?php
include __DIR__ . '/../vendor/autoload.php';

use G\Gearman\Builder;

$tasks = Builder::createTasks();

$tasks->onSuccess(function (\GearmanTask $task, $context) {
    $out = is_callable($context) ? $context($task) : $task->data();
    echo "onSuccess response: " . $out . " id: {$task->unique()}\n";
});

$tasks->onException(function (\GearmanTask $task) {
    echo "onException response {$task->data()}\n";
});

$responseParser = function (\GearmanTask $task) {
    return "Hello " . $task->data();
};

$tasks->addTask('fast.process', "fast1", $responseParser, 'g1');
$tasks->addTaskHigh('slow.process', "slow1", null, 'xxxx');
$tasks->addTask('fast.process', "fast2");
$tasks->addTask('exception.process', 'hi');

$tasks->runTasks();