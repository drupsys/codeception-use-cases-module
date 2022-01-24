# OperationIsNotCallable

You will get this error when you define a staged operation that is not callable, e.g.

you are calling `StageRunner::run` with these operations
```php
StageRunner::run($state, [
    'SET_NOW' => 'something that is not callable',
]),
```

instead of

```php
StageRunner::run($state, [
    'SET_NOW' => function (array $request): array {
        Carbon::setTestNow($request['now']);
        return [];
    },
]),
```
