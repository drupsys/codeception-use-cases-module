<?php

namespace MVF\Codeception\UseCases;

use GuzzleHttp\Utils;
use RuntimeException;
use MVF\Codeception\UseCases\Contracts\ActionResults;
use MVF\Codeception\UseCases\Contracts\ActorInterface;
use MVF\Codeception\UseCases\ValueObjects\TestResult;
use Throwable;

class Results implements ActionResults
{
    private array $validProperties = ['response', 'state', 'database', 'exception'];

    private ActorInterface $I;
    private TestResult $actual;

    public function __construct(ActorInterface $I, TestResult $actual)
    {
        $this->I = $I;
        $this->actual = $actual;
    }

    public function getActual(): TestResult
    {
        return $this->actual;
    }

    public function expectException(string $name = null): void
    {
        $exception = $this->actual->getException();
        if ($exception === null) {
            $this->I->fail('Expected exception but it was not thrown');
        }

        if ($name === null) {
            $this->actual = new TestResult(
                $this->actual->getResponse(),
                $this->actual->getState(),
                $this->actual->getDatabase(),
            );
        } elseif ($exception instanceof $name) {
            $this->actual = new TestResult(
                $this->actual->getResponse(),
                $this->actual->getState(),
                $this->actual->getDatabase(),
            );
        }
    }

    public function offsetExists($offset): bool
    {
        if (in_array($offset, $this->validProperties)) {
            return true;
        }

        return false;
    }

    public function offsetGet($offset)
    {
        switch ($offset) {
            case 'response':
                return $this->actual->getResponse();
            case 'state':
                return $this->actual->getState();
            case 'database':
                return $this->actual->getDatabase();
            case 'exception':
                return $this->actual->getException();
        }

        throw new RuntimeException(sprintf(
            "invalid property '%s', use one of %s",
            $offset,
            Utils::jsonEncode($this->validProperties),
        ));
    }

    public function offsetSet($offset, $value)
    {
        switch ($offset) {
            case 'response':
                $this->actual = new TestResult(
                    $value,
                    $this->actual->getState(),
                    $this->actual->getDatabase(),
                    $this->actual->getException(),
                );
                break;
            case 'state':
                $this->actual = new TestResult(
                    $this->actual->getResponse(),
                    $value,
                    $this->actual->getDatabase(),
                    $this->actual->getException(),
                );
                break;
            case 'database':
                $this->actual = new TestResult(
                    $this->actual->getResponse(),
                    $this->actual->getState(),
                    $value,
                    $this->actual->getException(),
                );
                break;
            case 'exception':
                $this->actual = new TestResult(
                    $this->actual->getResponse(),
                    $this->actual->getState(),
                    $this->actual->getDatabase(),
                    $value,
                );
                break;
        }

        throw new RuntimeException(sprintf(
            "invalid property '%s', use one of %s",
            $offset,
            Utils::jsonEncode($this->validProperties),
        ));
    }

    public function offsetUnset($offset)
    {
        switch ($offset) {
            case 'response':
                $this->actual = new TestResult(
                    [],
                    $this->actual->getState(),
                    $this->actual->getDatabase(),
                    $this->actual->getException(),
                );
                break;
            case 'state':
                $this->actual = new TestResult(
                    $this->actual->getResponse(),
                    [],
                    $this->actual->getDatabase(),
                    $this->actual->getException(),
                );
                break;
            case 'database':
                $this->actual = new TestResult(
                    $this->actual->getResponse(),
                    $this->actual->getState(),
                    [],
                    $this->actual->getException(),
                );
                break;
            case 'exception':
                $this->actual = new TestResult(
                    $this->actual->getResponse(),
                    $this->actual->getState(),
                    $this->actual->getDatabase(),
                    null,
                );
                break;
        }

        throw new RuntimeException(sprintf(
            "invalid property '%s', use one of %s",
            $offset,
            Utils::jsonEncode($this->validProperties),
        ));
    }

    function getResponse(): array
    {
        return $this->actual->getResponse();
    }

    function getState(): array
    {
        return $this->actual->getState();
    }

    function getDatabase(): array
    {
        return $this->actual->getDatabase();
    }

    function getException(): ?Throwable
    {
        return $this->actual->getException();
    }
}
