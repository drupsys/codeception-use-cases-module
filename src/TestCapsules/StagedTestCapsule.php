<?php

namespace MVF\Codeception\UseCases\TestCapsules;

use Carbon\Carbon;
use MVF\Codeception\UseCases\Exceptions\InvalidStageProvided;
use MVF\Codeception\UseCases\MultiStage\StageRunner;
use MVF\Codeception\UseCases\TestCapsule;
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

    public function entrypoint(array $state, array $request): array
    {
        $defaultTime = Carbon::now()->toDateTime();

        $runners = map($this->stages, $this->convertToCallableRunners());

        try {
            $result = StageRunner::build($state, $runners)->start();
            unset($state['stages']);

            return [
                'exception' => $result['exception'],
                'response' => [],
                'state' => array_replace_recursive($state, [
                    'stages' => $result['responses'],
                ]),
            ];
        } catch (\Throwable $exception) {
            return [
                'exception' => $exception,
                'state' => $state,
            ];
        } finally {
            Carbon::setTestNow($defaultTime);
        }
    }

    private function convertToCallableRunners(): callable
    {
        return function ($stage) {
            if (is_callable($stage)) {
                return $stage;
            } elseif ($stage instanceof SimpleTestCapsule) {
                return function (array $request, array $state) use ($stage) {
                    $result = $stage->entrypoint($state, $request);
                    if (isset($result['exception'])) {
                        throw $result['exception'];
                    }

                    return $result['response'];
                };
            }

            throw new InvalidStageProvided(get_class($stage));
        };
    }
}
