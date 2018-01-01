<?php

namespace Nonetallt\Joptimize;

class JoptimizeParameters
{
    private $params;

    public function __construct()
    {
        $this->params = [];
    }

    public function __get($name)
    {
        if(!isset($this->params[$name])) throw new \Exception("Parameter '$name' is not defined.");
        return $this->params[$name]->current();
    }

    public function defineEnum(array $args)
    {
        $parameter = new EnumParameter($args);
        $this->append($parameter);
    }

    public function defineLinear(array $args)
    {
        $parameter = new LinearParameter($args);
        $this->append($parameter);
    }

    public function defineRange(array $args)
    {
        $parameter = new RangeParameter($args);
        $this->append($parameter);
    }

    public function append(JoptimizeParameter $param)
    {
        $this->params[$param->getName()] = $param;
    }
    
    public function isEmpty()
    {
        return empty($this->params);
    }

    public function getParameters()
    {
        return $this->params;
    }

    public function getParameter(string $name)
    {
        return $this->params[$name] ?? null;
    }
}
