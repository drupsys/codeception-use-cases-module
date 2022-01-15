<?php

namespace MVF\Codeception\UseCases\Exceptions;

use RuntimeException;

class InvalidProviderImplementation extends RuntimeException
{
    public function __construct(string $implementation, string $expectedImplementation)
    {
        $docs = 'https://github.com/drupsys/codeception-use-cases-module/blob/main/docs/exceptions/InvalidProviderImplementation.md';
        parent::__construct("Provider '$implementation' does not implement '$expectedImplementation', read documentation $docs");
    }
}
