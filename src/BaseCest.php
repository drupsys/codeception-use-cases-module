<?php

namespace MVF\UseCases;

use Codeception\Example;
use Carbon\Carbon as CarbonTime;
use DateTime;
use GuzzleHttp\Utils;
use MVF\UseCases\Contracts\ActorInterface;
use MVF\UseCases\Contracts\TesterInterface;
use MVF\UseCases\Exceptions\InvalidActorProvided;
use Throwable;
use function Functional\each;
use function Functional\last;
use function Functional\map;

abstract class BaseCest
{
    protected abstract function testCases(): array;
    protected abstract function tester(): TesterInterface;

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
                Config::mysql()->table($table)->truncate();
            });
            Config::redis()->del('maxwell');
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
        $changes = array_values(Config::redis()->xRange('maxwell', '-', '+'));
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

    private function withDatabase(ActorInterface $I, array $inputs, callable $tester): array
    {
        if (isset($inputs['database'])) {
            $this->setup($inputs['database']);

            try {
                $actual = $tester();
            } finally {
                usleep(50000); // wait a little for bin logs to catchup
                $actual['database'] = $this->readBinLogs($I, $inputs['database']);
                $this->reset($actual['database']);
            }

            return $actual;
        } else {
            return $tester();
        }
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
            throw new InvalidActorProvided();
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

        $actual = $actionResults->getActual();
        if (isset($actual['exception'])) {
            throw $actual['exception'];
        } elseif ($maybeRuntimeException) {
            throw $maybeRuntimeException;
        } elseif ($maybeAssertionException) {
            throw $maybeAssertionException;
        }
    }
}
