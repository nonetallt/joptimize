<?php

namespace Nonetallt\Joptimize;

class RangeParameter extends JoptimizeParameter
{
    private $scope;

    public function __construct(array $args)
    {
        parent::__construct($args);
    }

    public function rewind()
    {
        $this->iteration = 0;
        $this->values = [];
        $this->set(0, $this->min());
        $this->set(1, $this->max() / 2);
        $this->set(2, $this->max());
        $this->times = [];
    }

    private function set(int $iteration = null, $value = null, bool $done = false)
    {
        /* Append if not defined */
        $iteration = $iteration ?? count($this->values);

        /* Use existing value if defined */
        if(isset($this->values[$iteration]['value'])) $value = $value ?? $this->values[ $iteration ]['value'];

        $this->values[$iteration] = [ 'value' => $value, 'done' => $done ];
    }

    public function current()
    {
        /* Create new values depending on execution time */
        if(!isset($this->values[$this->iteration]))
        {
            /* First new values required */
            if($this->iteration === 3)
            {
                $this->set(0, null, true);
                $this->set(1, null, true);
                $this->set(2, null, true);
                $this->set(null, ($this->values[1]['value'] + $this->min()) / 2 );
                $this->set(null, $this->values[1]['value'] / 2 + $this->values[1]['value']);
            }
            else
            {
                $newValues = [];
                /* Afterwards use the last 2 values */
                foreach($this->values as $index => $value)
                {
                    /* Skip processed values */
                    if($value['done']) continue;

                    /* Create new values from unprocessed ones and set them as processed */
                    /* $newValues[] = $this->min() + ($value['value'] / 2); */
                    $newValues[] = $value['value'] + ($this->min() - $value['value']) / 2;
                    $newValues[] = $value['value'] + ($this->max() - $value['value']) / 2;
                    $this->set($index, null, true);
                }
                foreach($newValues as $value)
                {
                    $this->set(null, $value);
                }
            }
        }
        return $this->values[$this->iteration]['value'];
    }

    public function valid()
    {
        /* Check if should stop trying to find better result */
        return $this->iteration <= $this->maxIterations();
    }

    private function min()
    {
        return $this->parameters[0];
    }

    private function max()
    {
        return $this->parameters[1];
    }

    private function maxIterations()
    {
        return $this->parameters[2] + 2;
    }

    public function getValues()
    {
        return $this->values;
    }
}
