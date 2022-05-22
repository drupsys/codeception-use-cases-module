<?php

namespace MVF\Codeception\UseCases\ValueObjects;

use Throwable;

class TestResult extends EntrypointResult
{
    private array $database;

    public function __construct(array $response, array $state, array $database, ?Throwable $exception = null)
    {
        parent::__construct($response, $state, $exception);

        $this->database = $database;
    }

    public function getDatabase(): array
    {
        return $this->database;
    }
}
