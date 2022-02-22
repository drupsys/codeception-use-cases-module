<?php

namespace MVF\Codeception\UseCases\StateFull;

use MVF\Codeception\UseCases\Contracts\ActionInterface;
use MVF\Codeception\UseCases\Contracts\DoubleInterface;
use MVF\Codeception\UseCases\Contracts\TesterInterface;
use Throwable;

class TestCapsule implements TesterInterface
{
    private string $actionClass;
    private string $doubleClass;
    private ActionInterface $action;
    private DoubleInterface $double;

    public function __construct(string $actionClass, string $doubleClass)
    {
        $this->actionClass = $actionClass;
        $this->doubleClass = $doubleClass;
    }

    function entrypoint(array $state, array $request): array
    {
        $this->double = new ($this->doubleClass)($state);

        try {
            $this->action = new ($this->actionClass)($this->double);

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
