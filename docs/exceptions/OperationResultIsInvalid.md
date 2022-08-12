# OperationResultIsInvalid

This happens when a stage in a staged test capsule does not return `MVF\Codeception\UseCases\ValueObjects\OperationResult` for example you might have something like this

```php
protected function tester(): TestCapsule
{
    return StagedTestCapsule::build()
        ->define('DO_SOMETHING', function (array $request, array $state) {
            // your operation would be here

            return [...]; // <- this is where the issue is
        });
}
```

To resolve this exception make sure you are returning an instance of `OperationResult`, e.g.

```php
use MVF\Codeception\UseCases\ValueObjects\OperationResult;

...

protected function tester(): TestCapsule
{
    return StagedTestCapsule::build()
        ->define('DO_SOMETHING', function (array $request, array $state) {
            // your operation would be here

            return new OperationResult(
                [...], // response of your operation
                $state,
            );
        });
}
```
