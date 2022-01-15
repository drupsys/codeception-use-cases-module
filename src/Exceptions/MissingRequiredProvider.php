<?php

namespace MVF\Codeception\UseCases\Exceptions;

use RuntimeException;

class MissingRequiredProvider extends RuntimeException
{
    public function __construct(string $provider)
    {
        parent::__construct("Missing required provider '$provider'");
    }
}
