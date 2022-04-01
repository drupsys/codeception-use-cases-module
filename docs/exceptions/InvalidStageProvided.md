# InvalidStageProvided

You will get this error if you are using `StagedTestCapsule` incorrectly, e.g. you have something like this

```php
protected function tester(): TestCapsule
{
    return StagedTestCapsule::build()
        ->define('CREATE_FUTURE_CAPS', 'invalid');
}
```

The second argument of define function must be either an instance of `StagedTestCapsule` or `callable`, e.g. valid example

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
