<?php

namespace MVF\Codeception\UseCases\ValueObjects;

use Throwable;

class EntrypointResult
{
    private array $response;
    private array $state;
    private ?Throwable $exception;

    public function __construct(array $response, array $state, ?Throwable $exception = null)
    {
        $this->response = $response;
        $this->state = $state;
        $this->exception = $exception;
    }

    public function getResponse(): array
    {
        return $this->response;
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
