<?php

namespace MVF\Codeception\UseCases\TestCapsules;

use MVF\Codeception\UseCases\Contracts\DoubleInterface;
use MVF\Codeception\UseCases\TestCapsule;
use MVF\Codeception\UseCases\ValueObjects\EntrypointResult;
use MVF\Servicer\Contracts\Action;
use ReflectionClass;
use RuntimeException;
use Throwable;

class SimpleTestCapsule extends TestCapsule
{
    private ReflectionClass $action;
    private ?ReflectionClass $double = null;

    private function __construct(string $actionClass, ?string $doubleClass = null)
    {
        $this->action = new ReflectionClass($actionClass);

        if (isset($doubleClass)) {
            $this->double = new ReflectionClass($doubleClass);
        }
    }

    public static function build(string $actionClass, ?string $doubleClass = null): self
    {
        return new self($actionClass, $doubleClass);
    }

    public function entrypoint(array $state, array $request): EntrypointResult
    {
        if ($this->action->implementsInterface(Action::class) === false) {
            throw new RuntimeException(
                sprintf("'%s' must implement '%s'", $this->action->getName(), Action::class),
            );
        }

        if ($this->double) {
            if ($this->double->implementsInterface(DoubleInterface::class) === false) {
                throw new RuntimeException(
                    sprintf("'%s' must implement '%s'", $this->double->getName(), DoubleInterface::class),
                );
            }

            return $this->stateful($state, $request);
        } else {
            return $this->stateless($state, $request);
        }
    }

    private function stateless(array $state, array $request): EntrypointResult
    {
        try {
            $action = $this->action->newInstance();

            return new EntrypointResult($action->handler($request), $state);
        } catch (Throwable $exception) {
            return new EntrypointResult([], $state, $exception);
        }
    }

    private function stateful(array $state, array $request): EntrypointResult
    {
        $double = $this->double->newInstance($state);

        try {
            $action = $this->action->newInstance($double);

            return new EntrypointResult($action->handler($request), $double->getState());
        } catch (Throwable $exception) {
            return new EntrypointResult([], $double->getState(), $exception);
        }
    }
}
