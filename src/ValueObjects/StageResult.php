<?php

namespace MVF\Codeception\UseCases\ValueObjects;

class StageResult extends OperationResult
{
    private string $name;

    public function __construct(string $name, array $result, array $state)
    {
        parent::__construct($result, $state);

        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
