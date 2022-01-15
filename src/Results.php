<?php

namespace MVF\Codeception\UseCases;

use MVF\Codeception\UseCases\Contracts\ActionResults;
use MVF\Codeception\UseCases\Contracts\ActorInterface;

class Results implements ActionResults
{
    private ActorInterface $I;
    private array $actual;

    public function __construct(ActorInterface $I, array $actual)
    {
        $this->I = $I;
        $this->actual = $actual;
    }

    public function getActual(): array
    {
        return $this->actual;
    }

    public function expectException(string $name = null): void
    {
        if (!isset($this->actual['exception'])) {
            $this->I->fail('Expected exception but it was not thrown');
        }

        if ($name === null) {
            unset($this->actual['exception']);
        } elseif ($this->actual['exception'] instanceof $name) {
            unset($this->actual['exception']);
        }
    }

    public function offsetExists($offset): bool
    {
        return isset($this->actual[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->actual[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->actual[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->actual[$offset]);
    }
}
