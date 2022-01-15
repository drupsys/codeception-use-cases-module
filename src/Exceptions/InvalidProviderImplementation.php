<?php

namespace MVF\Codeception\UseCases\Exceptions;

use RuntimeException;

class InvalidProviderImplementation extends RuntimeException
{
    public function __construct(string $implementation, string $expectedImplementation)
    {
        parent::__construct("Provider '$implementation' does not implement '$expectedImplementation', ");
    }
}
