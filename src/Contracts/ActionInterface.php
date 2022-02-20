<?php

namespace MVF\Codeception\UseCases\Contracts;

interface ActionInterface
{
    public function handler(array $request): array;
}
