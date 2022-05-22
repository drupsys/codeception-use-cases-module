<?php

namespace MVF\Codeception\UseCases\Exceptions;

use MVF\Codeception\UseCases\ValueObjects\OperationResult;

class OperationResultIsInvalid extends \RuntimeException
{
    public function __construct()
    {
        $resultClass = OperationResult::class;
        $docs = 'https://github.com/drupsys/codeception-use-cases-module/blob/main/docs/exceptions/OperationResultIsInvalid.md';
        parent::__construct("Operation must return '$resultClass' class, read documentation $docs");
    }
}
