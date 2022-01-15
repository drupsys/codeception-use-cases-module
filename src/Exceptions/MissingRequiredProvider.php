<?php

namespace MVF\Codeception\UseCases\Exceptions;

use RuntimeException;

class MissingRequiredProvider extends RuntimeException
{
    public function __construct(string $provider)
    {
        $docs = 'https://github.com/drupsys/codeception-use-cases-module/blob/main/docs/exceptions/MissingRequiredProvider.md';
        parent::__construct("Missing required provider '$provider', read documentation $docs");
    }
}
