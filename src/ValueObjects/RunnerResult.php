<?php

namespace MVF\Codeception\UseCases\ValueObjects;

use Throwable;

class RunnerResult
{
    private array $responses;
    private array $state;
    private ?Throwable $exception;

    public function __construct(array $responses, array $state, ?Throwable $exception)
    {
        $this->responses = $responses;
        $this->state = $state;
        $this->exception = $exception;
    }

    public function getResponses(): array
    {
        return $this->responses;
    }

    public function getState(): array
    {
        return $this->state;
    }

    public function getException(): ?Throwable
    {
        return $this->exception;
    }
}
