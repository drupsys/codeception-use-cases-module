<?php

namespace MVF\Codeception\UseCases\TestCapsules;

use Carbon\Carbon;
use MVF\Codeception\UseCases\Exceptions\InvalidStageProvided;
use MVF\Codeception\UseCases\MultiStage\StageRunner;
use MVF\Codeception\UseCases\TestCapsule;
use MVF\Codeception\UseCases\ValueObjects\EntrypointResult;
use MVF\Codeception\UseCases\ValueObjects\OperationResult;
use function Functional\map;

class StagedTestCapsule extends TestCapsule
{
    private array $stages = [];

    private function __construct()
    {
    }

    public static function build(): self
    {
        return new self();
    }

    /**
     * @param string $name
     * @param callable|SimpleTestCapsule $stage
     * @return $this
     */
    public function define(string $name, $stage): self
    {
        $this->stages[$name] = $stage;
        return $this;
    }

    public function entrypoint(array $state, array $request): EntrypointResult
    {
        $defaultTime = Carbon::now()->toDateTime();

        $runners = map($this->stages, $this->convertToCallableRunners());

        try {
            $result = StageRunner::build($state, $runners)->start();
            unset($state['stages']);

            return new EntrypointResult(
                [],
                array_replace_recursive($result->getState(), [
                    'stages' => $result->getResponses(),
                ]),
                $result->getException(),
            );
        } catch (\Throwable $exception) {
            return new EntrypointResult([], $state, $exception);
        } finally {
            Carbon::setTestNow($defaultTime);
        }
    }

    private function convertToCallableRunners(): callable
    {
        return function ($stage): callable {
            if (is_callable($stage)) {
                return $stage;
            } elseif ($stage instanceof SimpleTestCapsule) {
                return function (array $request, array $state) use ($stage) {
                    $result = $stage->entrypoint($state, $request);
                    if ($result->getException() !== null) {
                        throw $result->getException();
                    }

                    return new OperationResult($result->getResponse(), $result->getState());
                };
            }

            throw new InvalidStageProvided(get_class($stage));
        };
    }
}
