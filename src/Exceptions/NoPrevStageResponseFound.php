<?php


namespace MVF\Codeception\UseCases\Exceptions;


class NoPrevStageResponseFound extends \RuntimeException
{
    public function __construct(string $name)
    {
        $docs = 'https://github.com/drupsys/codeception-use-cases-module/blob/main/docs/exceptions/NoPrevStageResponseFound.md';
        parent::__construct("No prev stage response for operation '$name' was found, read documentation $docs");
    }
}
