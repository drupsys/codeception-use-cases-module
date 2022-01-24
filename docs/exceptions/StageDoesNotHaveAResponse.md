# StageDoesNotHaveAResponse

You will get this error when you try to get a response from a stage that never returned any response, e.g.

in your assertion you are using `StageIterator`

```php
'assert' => function (ActionTester $I, ActionResults $action) {
    $response = StageIterator::build($action)
        ->next('SET_NOW')
        ->response();
}
```

but the implementation of this operation does not return anything, e.g.
```php
StageRunner::run($state, [
    'SET_NOW' => function (array $request) {
        Carbon::setTestNow($request['now']);
    },
]),
```

to fix this update the implementation so that it would return an array, e.g.

```php
StageRunner::run($state, [
    Mocks::SET_NOW => function (array $request): array {
        Carbon::setTestNow($request['now']);
        return [];
    },
]),
```
