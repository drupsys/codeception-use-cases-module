<?php

namespace MVF\Codeception\UseCases\Contracts;

use ArrayAccess;
use Throwable;

interface ActionResults extends ArrayAccess
{
    function expectException(string $name = null): void;
    function getResponse(): array;
    function getState(): array;
    function getDatabase(): array;
    function getException(): ?Throwable;
}
