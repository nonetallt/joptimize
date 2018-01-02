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

