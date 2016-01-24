Gearman wrapper
======

Simple wrapper for gearman


Worker example

```php
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
```

Client example

```php
use G\Gearman\Builder;

$client = Builder::createClient();

$client->onSuccess(function ($response) {
    echo $response;
});

$client->doNormal('fast.process', "Hello");
```

Background client

```php
use G\Gearman\Builder;

$client = Builder::createClient();

$client->doBackground('slow.process', "Hello1");
$client->doBackground('slow.process', "Hello2");
$client->doBackground('slow.process', "Hello3");
```

Tasks

```php
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
```