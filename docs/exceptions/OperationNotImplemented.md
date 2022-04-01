# OperationNotImplemented

You will get this error if your test case has an operation that is not implemented in `StagedTestCapsule` or `StageRunner::run`, e.g.

your test case is something like this

```php
'inputs' => [
    'state' => [
        'stages' => [
            Mocks::setNow(Mocks::timeJustBeforeStartOfMonth('Europe/London')),
            ...
        ],
        ...
    ],
    ...
],
```

## StagedTestCapsule solution

in your test file, you have something like this

```php
class GrabSeismicContentsCest extends BaseCest
{
    ...

    protected function tester(): TestCapsule
    {
        return StagedTestCapsule::build()
            ->define('CREATE_FUTURE_CAPS', CreateFutureCaps::class, CreateFutureCapsDouble::class);
            // no SET_NOW stage implemented
    }
}
```

to fix this implement `SET_NOW` operation, like this

```php
class GrabSeismicContentsCest extends BaseCest
{
    ...

    protected function tester(): TestCapsule
    {
        return StagedTestCapsule::build()
            ->define('CREATE_FUTURE_CAPS', new SimpleTestCapsule(CreateFutureCaps::class, CreateFutureCapsDouble::class))
            ->define('SET_NOW', function (array $request, array $state): array {
                Carbon::setTestNow($request['now']);
                return [];
            });
    }
}
```

## StageRunner::run solution

in your test capsule, you have something like this

```php
return [
    'response' => [],
    'state' => [
        'stages' => StageRunner::run($state, [
            // no SET_NOW operation implemented
        ]),
    ],
];
```

to fix this implement `SET_NOW` operation, like this

```php
return [
    'response' => [],
    'state' => [
        'stages' => StageRunner::run($state, [
            'SET_NOW' => function (array $request): array {
                Carbon::setTestNow($request['now']);
                return [];
            },
        ]),
    ],
];
```
