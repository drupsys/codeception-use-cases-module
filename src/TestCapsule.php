<?php

namespace MVF\Codeception\UseCases;

abstract class TestCapsule
{
    abstract function entrypoint(array $state, array $request): array;

    function transformInitialDatabase(array $database): array
    {
        return $database;
    }

    function transformFinalDatabase(array $database): array
    {
        return $database;
    }
}
