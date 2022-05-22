<?php

namespace MVF\Codeception\UseCases;

use MVF\Codeception\UseCases\ValueObjects\EntrypointResult;

abstract class TestCapsule
{
    abstract function entrypoint(array $state, array $request): EntrypointResult;

    function transformInitialDatabase(array $database): array
    {
        return $database;
    }

    function transformFinalDatabase(array $database): array
    {
        return $database;
    }
}
