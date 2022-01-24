<?php

namespace MVF\Codeception\UseCases\MultiStage;

use MVF\Codeception\UseCases\Exceptions\OperationIsNotCallable;
use MVF\Codeception\UseCases\Exceptions\OperationNotImplemented;
use MVF\Codeception\UseCases\Exceptions\StageDoesNotHaveAnOperationName;
use MVF\Codeception\UseCases\Exceptions\StageDoesNotHaveARequest;

class StageRunner
{
    public static function run(array $stages, array $operations): array
    {
        $responses = [];
        foreach ($stages as $i => $stage) {
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
                'response' => $operation($stage['request']),
            ];
        }

        return $responses;
    }
}
