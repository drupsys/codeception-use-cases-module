<?php

namespace MVF\Codeception\UseCases\Exceptions;

use RuntimeException;

class InvalidStageProvided extends RuntimeException
{
    public function __construct(string $instanceClass)
    {
        $docs = 'https://github.com/drupsys/codeception-use-cases-module/blob/main/docs/exceptions/InvalidStageProvided.md';
        parent::__construct("Stage of type '$instanceClass' is not supported, read documentation $docs");
    }
}
