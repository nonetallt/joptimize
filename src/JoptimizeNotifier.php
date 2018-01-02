<?php

namespace Nonetallt\Joptimize;

class JoptimizeNotifier
{
    private $onIterationStart;
    private $onIterationEnd;
    private $onLastIteration;
    private $onFirstIteration;
    private $customValues;

    const MAPPING = [
        0 => 'iteration',
        1 => 'value',
        2 => 'totalIterations',
        3 => 'time',
        4 => 'bestTime',
        5 => 'name',
        6 => 'bestValue'
    ];
        
    public function __construct()
    {
        $this->customValues = [];
    }

    public function setFirstIteration(callable $cb)
    {
        $this->onFirstIteration = $cb;
    }

    public function setLastIteration(callable $cb)
    {
        $this->onLastIteration = $cb;
    }


    public function setIterationStart(callable $cb)
    {
        $this->onIterationStart = $cb;
    }

    public function setIterationEnd(callable $cb)
    {
        $this->onIterationEnd = $cb;
    }

    private function mapParams(array $params)
    {
        $result = [];
        foreach($params as $index => $param)
        {
            $result[self::MAPPING[$index]] = $param;
        }
        return $result;
    }

    public function lastIteration(...$params)
    {
        $this->iteration('onLastIteration', $params);
    }

    public function firstIteration(...$params)
    {
        $this->iteration('onFirstIteration', $params);
    }

    public function iterationStart(...$params)
    {
        $this->iteration('onIterationStart', $params);
    }

    public function iterationEnd(...$params)
    {
        $this->iteration('onIterationEnd', $params);
    }

    private function iteration(string $name, array $params)
    {
        if(is_null($this->$name)) return;

        $info = new NotifierInfo($this->mapParams($params));
        $info->setCustomValues($this->customValues);
        $this->$name->__invoke($info);

        /* Save custom values that were created */
        if($info->hasCustomValues()) $this->customValues = array_merge($info->getCustomValues());

    }
}
