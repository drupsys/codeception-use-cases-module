<?php


namespace MVF\Codeception\UseCases\Exceptions;


class StageDoesNotHaveAnOperationName extends \RuntimeException
{
    public function __construct()
    {
        $docs = 'https://github.com/drupsys/codeception-use-cases-module/blob/main/docs/exceptions/StageDoesNotHaveAnOperationName.md';
        parent::__construct("Encountered a stage with no operation name defined, read documentation $docs");
    }
}
