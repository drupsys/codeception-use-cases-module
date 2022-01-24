<?php


namespace MVF\Codeception\UseCases\Exceptions;


class StageDoesNotHaveARequest extends \RuntimeException
{
    public function __construct(string $name)
    {
        $docs = 'https://github.com/drupsys/codeception-use-cases-module/blob/main/docs/exceptions/StageDoesNotHaveARequest.md';
        parent::__construct("Stage '$name' does not have a request, read documentation $docs");
    }
}
