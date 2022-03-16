<?php

namespace MVF\Codeception\UseCases\TestCapsules;

use MVF\Codeception\UseCases\Contracts\ActionInterface;
use MVF\Codeception\UseCases\TestCapsule;
use ReflectionClass;
use RuntimeException;
use Throwable;

class StateLessTestCapsule extends TestCapsule
{
    private ReflectionClass $action;

    public function __construct(string $actionClass)
    {
        $this->action = new ReflectionClass($actionClass);
    }

    function entrypoint(array $state, array $request): array
    {
        if ($this->action->implementsInterface(ActionInterface::class) === false) {
            throw new RuntimeException('interface not implemented');
        }

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
}
