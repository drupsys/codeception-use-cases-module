<?php


namespace MVF\Codeception\UseCases\Exceptions;


class StageDoesNotHaveAResponse extends \RuntimeException
{
    public function __construct(string $name)
    {
        $docs = 'https://github.com/drupsys/codeception-use-cases-module/blob/main/docs/exceptions/StageDoesNotHaveAResponse.md';
        parent::__construct("Stage '$name' does not have a response, read documentation $docs");
    }
}
