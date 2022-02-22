<?php

namespace MVF\Codeception\UseCases\TestCapsules;

use MVF\Codeception\UseCases\Contracts\ActionInterface;
use MVF\Codeception\UseCases\TestCapsule;
use Throwable;

class StateLessTestCapsule extends TestCapsule
{
    private ActionInterface $action;

    public function __construct(string $actionClass)
    {
        $this->action = new $actionClass();
    }

    function entrypoint(array $state, array $request): array
    {
        try {
            return [
                'state' => $state,
                'response' => $this->action->handler($request),
            ];
        } catch (Throwable $exception) {
            return [
                'state' => $state,
                'exception' => $exception,
            ];
        }
    }
}
