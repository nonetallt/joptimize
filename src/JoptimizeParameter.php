<?php

namespace Nonetallt\Joptimize;

abstract class JoptimizeParameter implements \Iterator
{
    protected $name;
    protected $parameters;
    protected $iteration;
    
    public function __construct(array $args)
    {
        $type = gettype($args[0]);
        if(! is_string($args[0])) throw new \Exception("First argument given to 'define' method must be of type string, '$type' given.");

        $this->name = $args[0];

        /* Remove the name from arguments */
        array_splice($args, 0, 1);

        $this->parameters = $args;
        $this->iteration  = 0;
    }

    public function getName()
    {
        return $this->name;
    }

    public function rewind() 
    {
        $this->iteration = 0;
    }

    public function key()
    {
        return $this->iteration;
    }

    public function next()
    {
        ++$this->iteration;
    }
}
