<?php

namespace MVF\Codeception\UseCases;

use MVF\Codeception\UseCases\Contracts\ActionResults;
use MVF\Codeception\UseCases\Contracts\ActorInterface;
use MVF\Codeception\UseCases\Contracts\ReadableInterface;
use MVF\Codeception\UseCases\ValueObjects\EntrypointResult;

class Results implements ActionResults
{
    private ActorInterface $I;
    private EntrypointResult $actual;

    public function __construct(ActorInterface $I, EntrypointResult $actual)
    {
        $this->I = $I;
        $this->actual = $actual;
    }

    public function getActual(): EntrypointResult
    {
        return $this->actual;
    }

    public function expectException(string $name = null): void
    {
        $exception = $this->actual->getException();
        if ($exception === null) {
            $this->I->fail('Expected exception but it was not thrown');
        } else if ($exception instanceof ReadableInterface) {
            $this->I->fail('Error: ' . $exception->toString());
        }

        if ($name === null) {
            $this->actual = new EntrypointResult(
                $this->actual->getResponse(),
                $this->actual->getState(),
            );
        } elseif ($exception instanceof $name) {
            $this->actual = new EntrypointResult(
                $this->actual->getResponse(),
                $this->actual->getState(),
            );
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
