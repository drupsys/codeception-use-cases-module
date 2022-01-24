# OperationNotImplemented

You will get this error if your test case has an operation that is not implemented in `StageRunner::run`, e.g.

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

and in your test capsule, you have something like this

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
