<?php

namespace MVF\Codeception\UseCases\Exceptions;

use MVF\Codeception\UseCases\Contracts\ActorInterface;
use RuntimeException;

class InvalidActorProvided extends RuntimeException
{
    public function __construct(string $instanceClass)
    {
        $className = ActorInterface::class;
        $docs = 'https://github.com/drupsys/codeception-use-cases-module/blob/main/docs/exceptions/InvalidActorProvided.md';
        parent::__construct("Actor '$instanceClass' must implement '$className', read documentation $docs");
    }
}
