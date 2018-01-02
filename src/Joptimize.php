<?php

namespace Nonetallt\Joptimize;

class Joptimize
{
    private $params;
    private $notifier;
    private $initializationValues;

    public function __construct(array $initializationValues = [])
    {
        $this->params = new JoptimizeParameters();
        $this->notifier = new JoptimizeNotifier();
        $this->initializationValues = new NotifierInfo($initializationValues);
    }

    public function optimize(callable $cb)
    {
        /* Check that there is anything to optimize */
        if($this->params->isEmpty()) throw new \Exception('No parameters defined, nothing to optimize.');

        $results = [];

        foreach($this->params->getParameters() as $name => $param)
        {
            $results[$name] = $this->optimizeParameter($cb, $param, $name);   
            $param->rewind();
        }
        
        return $results; 
    }

    private function optimizeParameter(callable $cb, $param, string $name)
    {
        $bestTime = null;
        $bestValue= null;

        foreach($param as $key => $option)
        {
            $info = [
                $key, 
                $option, 
                $param->maxIterations(),
                $time ?? null,
                $bestTime ?? null,
                $name,
                $bestValue ?? null
            ];

            /* First iteration */
            if($key === 0) $this->notifier->firstIteration(...$info);

            /* Notify iteration start */
            $this->notifier->iterationStart(...$info);

            $time = $this->executionTime(function() use($cb, $key){
                $cb($this->params, $key, $this->initializationValues);
            });

            if(is_null($bestTime) || $time < $bestTime)
            {
                $bestTime = $time;
                $bestValue = $option;
            }

            /* Notify iteration completion */
            $this->notifier->iterationEnd(...$info);

            /* Last iteration */
            if($param->maxIterations() === $key +1) $this->notifier->lastIteration(...$info);
        }

        return $bestValue;
    }

    public function executionTime(callable $cb)
    {
        $start = $this->time();
        $cb();
        $end = $this->time();
        return $end - $start;
    }

    public function onFirstIteration(callable $cb)
    {
        $this->notifier->setFirstIteration($cb);
    }

    public function onIterationStart(callable $cb)
    {
        $this->notifier->setIterationStart($cb);
    }

    public function onIterationEnd(callable $cb)
    {
        $this->notifier->setIterationEnd($cb);
    }

    public function onLastIteration(callable $cb)
    {
        $this->notifier->setLastIteration($cb);
    }

    /* Forward calls starting with 'define' to parameters object */
    public function __call($name, $arguments)
    {
        $expected = 'define';
        if(substr($name, 0, strlen($expected)) !== $expected) throw new \Exception("Unknown method '$name'.");
        return $this->params->$name($arguments);
    }

    private function time()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    } 

}
