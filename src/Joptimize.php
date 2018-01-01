<?php

namespace Nonetallt\Joptimize;

class Joptimize
{
    private $params;
    private $maxIterations;

    public function __construct(int $maxIterations = null)
    {
        $this->params = new JoptimizeParameters();
        $this->maxIterations = $maxIterations;
    }

    public function optimize(callable $cb)
    {
        /* Check that there is anything to optimize */
        if($this->params->isEmpty()) throw new \Exception('No parameters defined, nothing to optimize.');

        $results = [];

        foreach($this->params->getParameters() as $name => $param)
        {
            $results[$name] = $this->optimizeParameter($cb, $param);   
            $param->rewind();
        }
        
        return $results; 
    }

    private function optimizeParameter(callable $cb, $param)
    {
        $bestTime = null;
        $bestValue= null;

        foreach($param as $key => $option)
        {
            /* Get the correct iteration for range */
            /* if(is_a($param, RangeParameter::class)) $key -= 2; */

            $time = $this->executionTime(function() use($cb, $key){
                $cb($this->params, $key);
            });

            if(is_null($bestTime) || $time < $bestTime)
            {
                $bestTime = $time;
                $bestValue = $option;
            }
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
