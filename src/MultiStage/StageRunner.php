<?php

namespace MVF\Codeception\UseCases\MultiStage;

use MVF\Codeception\UseCases\Exceptions\NoStagesDefined;
use MVF\Codeception\UseCases\Exceptions\OperationIsNotCallable;
use MVF\Codeception\UseCases\Exceptions\OperationNotImplemented;
use MVF\Codeception\UseCases\Exceptions\OperationResultIsInvalid;
use MVF\Codeception\UseCases\Exceptions\StageDoesNotHaveAnOperationName;
use MVF\Codeception\UseCases\Exceptions\StageDoesNotHaveARequest;
use MVF\Codeception\UseCases\ValueObjects\OperationResult;
use MVF\Codeception\UseCases\ValueObjects\RunnerResult;
use MVF\Codeception\UseCases\ValueObjects\StageResult;

class StageRunner
{
    private array $state;
    private array $operations;

    private function __construct(array $state, array $operations)
    {
        if (!(isset($state['stages']))) {
            throw new NoStagesDefined();
        }

        $this->state = $state;
        $this->operations = $operations;
    }

    public static function build(array $state, array $operations): self {
        return new self($state, $operations);
    }

    public function start(): RunnerResult
    {
        $responses = [];
        $exception = null;
        $state = $this->state;

        foreach ($this->state['stages'] as $i => $stage) {
            try {
                $result = $this->startOperation($stage, $state);
                $state = $result->getState();
                $responses[$i] = [
                    'operation' => $result->getName(),
                    'response' => $result->getResponse(),
                ];
            } catch (\Throwable $exception) {
                break;
            }
        }

        return new RunnerResult($responses, $state, $exception);
    }

    private function startOperation(array $stage, array $state): StageResult
    {
        if (!isset($stage['operation'])) {
            throw new StageDoesNotHaveAnOperationName();
        }

        $operationName = $stage['operation'];
        if (!isset($this->operations[$operationName])) {
            throw new OperationNotImplemented($operationName);
        }

        $operation = $this->operations[$operationName];
        if (!is_callable($operation)) {
            throw new OperationIsNotCallable($operationName);
        }

        if (!isset($stage['request'])) {
            throw new StageDoesNotHaveARequest($operationName);
        }

        $result = $operation($stage['request'], $state);
        if (!($result instanceof OperationResult)) {
            throw new OperationResultIsInvalid();
        }

        return new StageResult($operationName, $result->getResponse(), $result->getState());
    }
}
