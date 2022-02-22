<?php

namespace MVF\Codeception\UseCases\Contracts;

interface DoubleInterface
{
    public function __construct(array $state);
    public function getState(): array;
}
