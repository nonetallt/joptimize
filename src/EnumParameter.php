<?php

namespace Nonetallt\Joptimize;

class EnumParameter extends JoptimizeParameter
{

    public function __construct(array $args)
    {
        parent::__construct($args);
    }

    public function current()
    {
        return $this->parameters[$this->iteration];
    }

    public function valid()
    {
        return isset($this->parameters[$this->iteration]);
    }
}
