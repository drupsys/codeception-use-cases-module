<?php

namespace MVF\Codeception\UseCases\Exceptions;

use RuntimeException;

class InvalidActorProvided extends RuntimeException
{
    public function __construct()
    {
        parent::__construct("You codeception actor must implement an ActorInterface");
    }
}
