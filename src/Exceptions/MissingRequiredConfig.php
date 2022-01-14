<?php

namespace MVF\UseCases\Exceptions;

use RuntimeException;

class MissingRequiredConfig extends RuntimeException
{
    public function __construct(string $type)
    {
        parent::__construct("Missing required config for $type object");
    }
}
