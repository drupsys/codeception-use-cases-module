<?php

namespace MVF\Codeception\UseCases\Contracts;

interface TesterInterface
{
    function entrypoint(array $state, array $request): array;
}
