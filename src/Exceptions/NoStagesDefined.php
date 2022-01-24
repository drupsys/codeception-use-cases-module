<?php


namespace MVF\Codeception\UseCases\Exceptions;


class NoStagesDefined extends \RuntimeException
{
    public function __construct()
    {
        $docs = 'https://github.com/drupsys/codeception-use-cases-module/blob/main/docs/exceptions/NoStagesDefined.md';
        parent::__construct("Your state does not have any stages defined, read documentation $docs");
    }
}
