<?php

namespace MVF\UseCases\Contracts;

interface TesterInterface
{
    function entrypoint(array $state, array $request): array;
}
