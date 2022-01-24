<?php


namespace MVF\Codeception\UseCases\Exceptions;


class NoNextStageResponseFound extends \RuntimeException
{
    public function __construct(string $name)
    {
        $docs = 'https://github.com/drupsys/codeception-use-cases-module/blob/main/docs/exceptions/NoNextStageResponseFound.md';
        parent::__construct("No next stage response for operation '$name' was found, read documentation $docs");
    }
}