# joptimize
Automatically optimize PHP execution time based on results achieved by tweaking parameters. Can be integrated to a Laravel framework application with the [Laravel wrapper](https://github.com/nonetallt/joptimize).

#Installation
```
composer require nonetallt/joptimize --dev
```

# Basic usage
```php
$optimizer = new Joptimize();
$optimizer->defineEnum('sleepTime', 1000, 2000, 3000, 4000);
$result = $optimizer->optimize(function($params) {
    usleep($params->sleepTime);
});

// Will return ['sleepTime' => 1000]
```
# Supported parameter types

**Enum** : test a group of values and find the fastest value
```php
$optimizer->defineEnum($name, ...$values);
```
---
**Linear** : test a all values from start to end and find the fastest value
```php
$optimizer->defineLinear($name, $start, $end, $stepSize = 1);
```
---
**Range** : close towards the fastest value in the range, increasing the
number of iterations will improve accuracy but take more executions to
calculate
```php
$optimizer->defineRange($name, $min, $max, $maxIterations);
```

# Initialization

You can pass a keyed array to the constructor to use values in the array in
context of the closure without having to recalculate values on each iteration.
This can be useful when there are many values that will make use() definition messy.

```php
$optimizer = new Joptimize(['example' => 1]);
$optimizer->defineEnum('sleepTime', 1000, 2000, 3000, 4000);
$result = $optimizer->optimize(function($params, $iteration, $init) {
    echo $init->example;
    // 1
});
```
# Logging progress using the notifier callbacks

Starting and completing of iterations can be tracked by using the following notifier
methods:
* onFirstIteration($callback) called before executing the first iteration
* onIterationStart($callback) called before each iteration
* onIterationEnd($callback)   called after each iteration
* onLastIteration($callback)  called after last iteration

The first argument passed to the notifier callback is the 'info' object, which
contains information accessible in the iteration. All of the info variables
might not be usable in all of the notify methods, for example, you cannot use the 'time'
variable to log the execution time if the iteration has not been executed yet (onFirst
and onStart methods).

```php
$optimizer = new Joptimize();
$optimizer->defineEnum('sleepTime', 1);
$optimizer->onFirstIteration(function($info) {
    echo $info->value; // 123
    echo $info->name; // 'sleepTime'
});
```

You may also save custom objects to the info container by calling the
saveValue($name, $value) method. This can be useful if you wish to use some
variables inside multiple notifier callbacks. For example, you could define
your log class variable in the first iteration, and use the log variable in other
notifier callbacks.

```php
$optimizer = new Joptimize();
$optimizer->defineEnum('sleepTime', 1);

$optimizer->onFirstIteration(function($info) {
    $info->saveValue('logger', new Logger());
});

$optimizer->onIterationStart(function($info) {
    $logger = $info->logger;
    $logger->log("{$info->value}");
});

```


