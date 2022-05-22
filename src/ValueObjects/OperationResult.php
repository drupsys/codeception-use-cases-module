<?php

namespace MVF\Codeception\UseCases\ValueObjects;

class OperationResult
{
    private array $response;
    private array $state;

    public function __construct(array $response, array $state)
    {
        $this->response = $response;
        $this->state = $state;
    }

    public function getResponse(): array
    {
        return $this->response;
    }

    public function getState(): array
    {
        return $this->state;
    }
}
