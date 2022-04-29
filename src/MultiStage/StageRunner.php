<?php

namespace MVF\Codeception\UseCases\MultiStage;

use MVF\Codeception\UseCases\Exceptions\NoStagesDefined;
use MVF\Codeception\UseCases\Exceptions\OperationIsNotCallable;
use MVF\Codeception\UseCases\Exceptions\OperationNotImplemented;
use MVF\Codeception\UseCases\Exceptions\StageDoesNotHaveAnOperationName;
use MVF\Codeception\UseCases\Exceptions\StageDoesNotHaveARequest;

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

    /**
     * @deprecated Use StageRunner::build(state, operations)->start() instead
     */
    public static function run(array $state, array $operations): array
    {
        if (!(isset($state['stages']))) {
            throw new NoStagesDefined();
        }

        $responses = [];
        foreach ($state['stages'] as $i => $stage) {
            if (!isset($stage['operation'])) {
                throw new StageDoesNotHaveAnOperationName();
            }

            $operationName = $stage['operation'];
            if (!isset($operations[$operationName])) {
                throw new OperationNotImplemented($operationName);
            }

            $operation = $operations[$operationName];
            if (!is_callable($operation)) {
                throw new OperationIsNotCallable($operationName);
            }

            if (!isset($stage['request'])) {
                throw new StageDoesNotHaveARequest($operationName);
            }

            $responses[$i] = [
                'operation' => $operationName,
                'response' => $operation($stage['request'], $state),
            ];
        }

        return $responses;
    }

    public function start(): array
    {
        $responses = [];
        $exception = null;

        foreach ($this->state['stages'] as $i => $stage) {
            try {
                $responses[$i] = $this->startOperation($stage);
            } catch (\Throwable $exception) {
                break;
            }
        }

        return [
            'responses' => $responses,
            'exception' => $exception,
        ];
    }

    private function startOperation(array $stage): array
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

        return [
            'operation' => $operationName,
            'response' => $operation($stage['request'], $this->state),
        ];
    }
}
