<?php

namespace Nonetallt\Joptimize;

class LinearParameter extends JoptimizeParameter
{
    private $values;

    public function __construct(array $args)
    {
        parent::__construct($args);
        $this->values = null;
    }

    public function current()
    {
        return $this->getValues()[$this->iteration];
    }

    public function valid()
    {
        return isset($this->getValues()[$this->iteration]);
    }

    private function min()
    {
        return $this->parameters[0];
    }

    private function max()
    {
        return $this->parameters[1];
    }

    private function stepSize()
    {
        return $this->parameters[2] ?? 1;
    }

    /* Use a lazy loader */
    public function getValues()
    {
        return $this->values ?? range($this->min(), $this->max(), $this->stepSize());
    }
}
