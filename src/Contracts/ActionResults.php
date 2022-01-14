<?php

namespace MVF\UseCases\Contracts;

use ArrayAccess;

interface ActionResults extends ArrayAccess
{
    public function expectException(string $name = null): void;
}
