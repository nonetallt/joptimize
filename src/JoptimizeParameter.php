<?php

namespace Nonetallt\Joptimize;

abstract class JoptimizeParameter implements \Iterator
{
    protected $name;
    protected $parameters;
    protected $iteration;
    
    public function __construct(array $args)
    {
        /* Use first arg for parameter name */
        $this->name = $args[0] ?? 'unnamed parameter';

        /* Check that required args have been given */
        $this->validateArgs($args);
        
        /* Remove the name from arguments */
        array_splice($args, 0, 1);

        $this->parameters = $args;
        $this->iteration  = 0;
    }

    private function validateArgs(array $args)
    {
        $index = 0;
        foreach($this->requiredArgs() as $name => $types)
        {
            if(!isset($args[$index])) throw new \Exception("Missing argument $index '$name' for parameter '$this->name'.");
            $given = gettype($args[$index]);
            $validTypes = explode('|', $types);
            $typesForHumans = implode(' or ', $validTypes);
            if(! in_array($given, $validTypes)) throw new \Exception("Argument $index '$name' requires $typesForHumans value. '$given' given.");
            $index++;
        }
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

    private function requiredArgs()
    {
        return array_merge(['name' => 'string'], $this->requiredParameters());
    }

    public abstract function maxIterations();

    protected abstract function requiredParameters();
}
