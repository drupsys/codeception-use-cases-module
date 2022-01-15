<?php

namespace MVF\Codeception\UseCases\Exceptions;

use RuntimeException;

class InvalidConnectionObject extends RuntimeException
{
    public function __construct(string $type)
    {
        parent::__construct("Invalid $type implementation");
    }
}
