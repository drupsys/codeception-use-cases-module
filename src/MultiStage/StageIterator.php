<?php

namespace MVF\Codeception\UseCases\MultiStage;

use MVF\Codeception\UseCases\Contracts\ActionResults;
use MVF\Codeception\UseCases\Exceptions\NoStagesDefined;
use MVF\Codeception\UseCases\Exceptions\StageDoesNotHaveAnOperationName;
use MVF\Codeception\UseCases\Exceptions\StageDoesNotHaveAResponse;

class StageIterator
{
    private array $stages;
    private int $index;

    public function __construct(ActionResults $action)
    {
        if (!isset($action['state']['stages'])) {
            throw new NoStagesDefined();
        }

        $this->stages = $action['state']['stages'];
        $this->index = 0;
    }

    public function next(string $operationName): self
    {
        for ($i = $this->index; $i < count($this->stages); $i++) {
            $stage = $this->stages[$i];
            if (!isset($stage['operation'])) {
                throw new StageDoesNotHaveAnOperationName();
            }

            if ($stage['operation'] === $operationName) {
                $this->index = $i;

                return $this;
            }
        }

        $this->index = 0;

        return $this;
    }

    public function prev(string $operationName): self
    {
        for ($i = $this->index; $i > 0; $i--) {
            $stage = $this->stages[$i];
            if (!isset($stage['operation'])) {
                throw new StageDoesNotHaveAnOperationName();
            }

            if ($stage['operation'] === $operationName) {
                $this->index = $i;

                return $this;
            }
        }

        $this->index = count($this->stages) - 1;

        return $this;
    }

    public function response(): array
    {
        $stage = $this->stages[$this->index];
        if (!isset($stage['operation'])) {
            throw new StageDoesNotHaveAnOperationName();
        }

        if (!isset($stage['response'])) {
            throw new StageDoesNotHaveAResponse($stage['operation']);
        }

        return $stage['response'];
    }
}
