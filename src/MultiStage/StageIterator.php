<?php

namespace MVF\Codeception\UseCases\MultiStage;

use MVF\Codeception\UseCases\Contracts\ActionResults;
use MVF\Codeception\UseCases\Exceptions\NoNextStageResponseFound;
use MVF\Codeception\UseCases\Exceptions\NoPrevStageResponseFound;
use MVF\Codeception\UseCases\Exceptions\NoStagesDefined;
use MVF\Codeception\UseCases\Exceptions\StageDoesNotHaveAnOperationName;
use MVF\Codeception\UseCases\Exceptions\StageDoesNotHaveAResponse;
use RuntimeException;
use function Functional\first;

class StageIterator
{
    private array $stages;
    private ?array $currentStage = null;
    private int $index;

    private function __construct(ActionResults $action)
    {
        if (!isset($action['state']['stages'])) {
            throw new NoStagesDefined();
        }

        $this->stages = $action['state']['stages'];
        $this->currentStage = first($this->stages);
        $this->index = 0;
    }

    public static function build(ActionResults $action): self {
        return new self($action);
    }

    public function next(string $operationName): self
    {
        if (!($this->index < count($this->stages))) {
            throw new NoNextStageResponseFound($operationName);
        }

        for ($i = $this->index; $i < count($this->stages); $i++) {
            $stage = $this->stages[$i];
            if (!isset($stage['operation'])) {
                throw new StageDoesNotHaveAnOperationName();
            }

            if ($stage['operation'] === $operationName) {
                $this->currentStage = $this->stages[$i];
                $this->index = $i + 1;

                return $this;
            }
        }

        $this->index = count($this->stages);
        throw new NoNextStageResponseFound($operationName);
    }

    public function prev(string $operationName): self
    {
        if (!($this->index > 0)) {
            throw new NoPrevStageResponseFound($operationName);
        }

        for ($i = $this->index; $i > 0; $i--) {
            $stage = $this->stages[$i];
            if (!isset($stage['operation'])) {
                throw new StageDoesNotHaveAnOperationName();
            }

            if ($stage['operation'] === $operationName) {
                $this->currentStage = $this->stages[$i];
                $this->index = $i - 1;

                return $this;
            }
        }

        $this->index = 0;
        throw new NoPrevStageResponseFound($operationName);
    }

    public function response(): array
    {
        $stage = $this->currentStage;
        if (!isset($stage)) {
            throw new RuntimeException('No current stage exists');
        }

        if (!isset($stage['operation'])) {
            throw new StageDoesNotHaveAnOperationName();
        }

        if (!isset($stage['response'])) {
            throw new StageDoesNotHaveAResponse($stage['operation']);
        }

        return $stage['response'];
    }
}
