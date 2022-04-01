<?php

namespace MVF\Codeception\UseCases\Traits;

trait DefaultDouble
{
    private array $state;

    public function __construct(array $state)
    {
        $this->state = $state;
    }

    public function getState(): array
    {
        return $this->state;
    }
}
