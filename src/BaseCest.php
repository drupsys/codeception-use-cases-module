<?php

namespace MVF\Codeception\UseCases;

use Codeception\Example;
use Carbon\Carbon as CarbonTime;
use DateTime;
use GuzzleHttp\Utils;
use MVF\Servicer\Contracts\Readable;
use RuntimeException;
use MVF\Codeception\UseCases\Contracts\ActorInterface;
use MVF\Codeception\UseCases\Exceptions\InvalidActorProvided;
use MVF\Codeception\UseCases\ValueObjects\EntrypointResult;
use MVF\Codeception\UseCases\ValueObjects\TestResult;
use Throwable;
use function Functional\each;
use function Functional\last;
use function Functional\map;

abstract class BaseCest
{
    protected abstract function testCases(): array;
    protected abstract function tester(): TestCapsule;

    private function tests(): array
    {
        CarbonTime::setTestNow(new DateTime('01-01-2000 00:00:00'));
        $testCases = $this->testCases();
        CarbonTime::setTestNow();

        $onlyTestCases = array_filter($testCases, function ($testCase) {
            return strpos($testCase['name'], 'only:') !== false;
        });

        return empty($onlyTestCases) ? $testCases : $onlyTestCases;
    }

    private function doNotCheckKeyConstraints(callable $callback): void
    {
        Config::mysql()->statement('SET FOREIGN_KEY_CHECKS=0;');
        $callback();
        Config::mysql()->statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function setup(array $databases): void
    {
        $this->doNotCheckKeyConstraints(function() use ($databases) {
            $this->reset($databases, false);
            each($databases, function (array $tableRows, string $table) {
                Config::mysql()->table($table)->insert(array_values($tableRows));
            });
        });
    }

    private function reset(array $databases, $dontCheckKeyConstraints = true): void
    {
        $reset = function() use ($databases, $dontCheckKeyConstraints) {
            each($databases, function (array $tableRows, string $table) {
                Config::mysql()->table($table)->delete();
                $wrappedTable = Config::mysql()->getQueryGrammar()->wrapTable($table);
                Config::mysql()->statement("ALTER TABLE $wrappedTable AUTO_INCREMENT = 1");
            });
            Config::redis()->del(Config::redisKey());
        };

        if ($dontCheckKeyConstraints) {
            $this->doNotCheckKeyConstraints($reset);
        } else {
            $reset();
        }
    }

    private function toColumnValue(array $row): callable
    {
        return function ($column) use ($row) {
            return $row[last(explode('.', $column))];
        };
    }

    private function readBinLogs(ActorInterface $I, array $inputDatabase): array
    {
        $changes = array_values(Config::redis()->xRange(Config::redisKey(), '-', '+'));
        foreach ($changes as $change) {
            $record = Utils::jsonDecode($change['message'], true);
            $table = sprintf('%s.%s', $record['database'], $record['table']);

            $identifiers = map($I->getKeyIdentifier($table), $this->toColumnValue($record['data']));
            $id = implode(':', $identifiers);

            switch ($record['type']) {
                case 'insert':
                case 'update':
                    foreach ($record['data'] as $column => $value) {
                        $inputDatabase[$table][$id]["$table.$column"] = $value;
                    }
                    break;
                case 'delete':
                    unset($inputDatabase[$table][$id]);
            }
        }

        return $inputDatabase;
    }

    private function withDatabase(ActorInterface $I, array $inputs, callable $tester): TestResult
    {
        $database = [];

        if (isset($inputs['database'])) {
            $database = $this->tester()->transformInitialDatabase($inputs['database']);
            $this->setup($database);

            try {
                $actual = $tester();
            } finally {
                usleep(50000); // wait a little for bin logs to catchup
                $database = $this->readBinLogs($I, $inputs['database']);
                $this->reset($database);
                $database = $this->tester()->transformFinalDatabase($database);
            }
        } else {
            $actual = $tester();
        }

        if (!($actual instanceof EntrypointResult)) {
            throw new RuntimeException('invalid entrypoint response');
        }

        return new TestResult(
            $actual->getResponse(),
            $actual->getState(),
            $database,
            $actual->getException(),
        );
    }

    /**
     * @dataProvider tests
     * @param mixed $I
     * @param Example $testCase
     * @throws Throwable
     */
    public function test($I, Example $testCase)
    {
        if (!($I instanceof ActorInterface)) {
            throw new InvalidActorProvided(get_class($I));
        }

        $I->wantTo($testCase['name']);

        if (!isset($testCase['inputs']) || !isset($testCase['assert'])) {
            $I->markTestIncomplete('Test is not implemented!');
            return;
        }

        $actual = $this->withDatabase($I, $testCase['inputs'], function () use ($testCase) {
            return $this->tester()->entrypoint(
                $testCase['inputs']['state'] ?? [],
                $testCase['inputs']['request'] ?? [],
            );
        });

        $actionResults = new Results($I, $actual);

        $maybeRuntimeException = null;
        $maybeAssertionException = null;

        try {
            $testCase['assert']($I, $actionResults);
        } catch (\PHPUnit\Framework\Exception $exception) {
            $maybeAssertionException = $exception;
        } catch (Throwable $exception) {
            $maybeRuntimeException = $exception;
        }

        $exception = $actionResults->getException();
        if ($exception instanceof Readable) {
            $I->fail('Error: ' . $exception->toString());
        } else if ($exception !== null) {
            throw $exception;
        } elseif ($maybeRuntimeException) {
            throw $maybeRuntimeException;
        } elseif ($maybeAssertionException) {
            throw $maybeAssertionException;
        }
    }
}
