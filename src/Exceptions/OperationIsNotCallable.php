<?php


namespace MVF\Codeception\UseCases\Exceptions;


class OperationIsNotCallable extends \RuntimeException
{
    public function __construct(string $name)
    {
        $docs = 'https://github.com/drupsys/codeception-use-cases-module/blob/main/docs/exceptions/OperationNotImplemented.md';
        parent::__construct("Operation for '$name' is not callable, read documentation $docs");
    }
}
