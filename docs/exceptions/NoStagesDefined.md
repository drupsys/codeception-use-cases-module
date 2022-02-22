# NoStagesDefined

There are 2 reasons you might encounter this error

## Reason 1

You test case inputs do not define stages, e.g.

you have something like this

```php
protected function testCases(): array
{
    return [
        [
            'inputs' => [
                'state' => [
                    // IMPORTANT! state does not have stages defined
                ],
                ...
            ],
            'name' => 'should do something',
            'assert' => function (ActionTester $I, ActionResults $action) {
                // assert something,
            },
        ],
        ...
    ]
}
```

instead of this

```php
protected function testCases(): array
{
    return [
        [
            'inputs' => [
                'state' => [
                    'stages' => [
                        // list of operations
                    ],
                ],
                ...
            ],
            'name' => 'should do something',
            'assert' => function (ActionTester $I, ActionResults $action) {
                // assert something,
            },
        ],
        ...
    ]
}
```

# Reason 2

Your capsule's `entrypoint` function does not return stages, e.g.

you have something like this

```php
class YourTestCapsule extends TestCapsule
{
    function entrypoint(array $state, array $request): array
    {
        try {
            return [
                'response' => [],
                'state' => [
                    // IMPORTANT! state does not have stages defined
                ],
            ];
        } catch (\Throwable $exception) {
            return [
                'exception' => $exception,
                'state' => [],
            ];
        }
    }
}
```

instead of

```php
class YourTestCapsule extends TestCapsule
{
    function entrypoint(array $state, array $request): array
    {
        try {
            return [
                'response' => [],
                'state' => [
                    'stages' => StageRunner::run($state, [
                        ...
                    ]),
                ],
            ];
        } catch (\Throwable $exception) {
            return [
                'exception' => $exception,
                'state' => [],
            ];
        }
    }
}
```
