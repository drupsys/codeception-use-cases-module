<?php

namespace MVF\Codeception\UseCases\TestCapsules;

use MVF\Codeception\UseCases\Contracts\ActionInterface;
use MVF\Codeception\UseCases\Contracts\DoubleInterface;
use MVF\Codeception\UseCases\TestCapsule;
use ReflectionClass;
use RuntimeException;
use Throwable;

class SimpleTestCapsule extends TestCapsule
{
    private ReflectionClass $action;
    private ?ReflectionClass $double = null;

    /**
     * @deprecated Use SimpleTestCapsule::build(actionClass, doubleClass) instead
     */
    public function __construct(string $actionClass, ?string $doubleClass = null)
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

    public function entrypoint(array $state, array $request): array
    {
        if ($this->action->implementsInterface(ActionInterface::class) === false) {
            throw new RuntimeException(
                sprintf("'%s' must implement '%s'", $this->action->getName(), ActionInterface::class),
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

    private function stateless(array $state, array $request): array
    {
        try {
            $action = $this->action->newInstance();

            return [
                'state' => $state,
                'response' => $action->handler($request),
            ];
        } catch (Throwable $exception) {
            return [
                'state' => $state,
                'exception' => $exception,
            ];
        }
    }

    private function stateful(array $state, array $request): array
    {
        $double = $this->double->newInstance($state);

        try {
            $action = $this->action->newInstance($double);

            return [
                'state' => $double->getState(),
                'response' => $action->handler($request),
            ];
        } catch (Throwable $exception) {
            return [
                'state' => $double->getState(),
                'exception' => $exception,
            ];
        }
    }
}
