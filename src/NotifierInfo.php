<?php

namespace Nonetallt\Joptimize;

class NotifierInfo
{
    private $values;
    private $customValues;
    
    public function __construct(array $values)
    {
        $this->values = $values;
        $this->customValues = [];
    }

    public function __get($name)
    {
        $value = $this->values[$name] ?? $this->customValues[$name] ?? null;
        /* if(is_null($value)) throw new \Exception("Undefined variable '$name'"); */
        return $value;
    }

    public function iterationsLeft()
    {
        return $this->totalIterations - ($this->iteration +1);
    }

    public function saveValue(string $name, $value)
    {
        $this->customValues[$name] = $value;
    }

    public function setCustomValues(array $values)
    {
        $this->customValues = $values;
    }

    public function getCustomValues()
    {
        return $this->customValues;
    }

    public function hasCustomValues()
    {
        return ! empty($this->getCustomValues());
    }
}
