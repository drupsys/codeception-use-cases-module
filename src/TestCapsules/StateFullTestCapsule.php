<?php

namespace MVF\Codeception\UseCases\TestCapsules;

use MVF\Codeception\UseCases\Contracts\ActionInterface;
use MVF\Codeception\UseCases\Contracts\DoubleInterface;
use MVF\Codeception\UseCases\TestCapsule;
use ReflectionClass;
use RuntimeException;
use Throwable;

class StateFullTestCapsule extends TestCapsule
{
    private ReflectionClass $action;
    private ReflectionClass $double;

    public function __construct(string $actionClass, string $doubleClass)
    {
        $this->action = new ReflectionClass($actionClass);
        $this->double = new ReflectionClass($doubleClass);
    }

    function entrypoint(array $state, array $request): array
    {
        if ($this->double->implementsInterface(DoubleInterface::class) === false) {
            throw new RuntimeException('interface not implemented');
        }

        $double = $this->double->newInstance($state);

        if ($this->action->implementsInterface(ActionInterface::class) === false) {
            throw new RuntimeException('interface not implemented');
        }

        try {
            $action = $this->action->newInstance($double);

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
}
