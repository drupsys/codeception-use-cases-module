# InvalidActorProvided

This error is displayed when your codeception actor does not implement `MVF\Codeception\UseCases\Contracts\ActorInterface`,

Your actor is usually different for each of your codeception suites, e.g. `integration`, `functional`, `unit`, etc. You can find your actor name in the `tests` folder `<type of test>.suite.yml` file. If you have `unit.suite.yml` file then the content of that file will be something like this.

```yaml
actor: UnitTester
modules:
    enabled:
        - \Helper\Unit
        - Asserts
```

`UnitTester` is the actor for this suite, you can find the implementation of this actor in `tests/_support/UnitTester.php` file.

To fix this error you need to find your `suite` that enables `\MVF\Codeception\UseCases\Module` and its actor, so for example, if you have `usecase.suite.yml` that looks something like this.

```yaml
actor: UseCaseTester
modules:
    enabled:
        - \MVF\Codeception\UseCases\Module:
            providers:
              mysql: App\Providers\UseCases\EloquentProvider
              redis: App\Providers\UseCases\RedisProvider
```

Then open `tests/_support/UseCaseTester.php` the content of this file should resemble the following

```php
<?php

/**
 * Inherited Methods
 *
 * @SuppressWarnings(PHPMD)
*/
class UseCaseTester extends \Codeception\Actor
{
    use _generated\UseCaseTesterActions;
}
```

then update this class so that it would implement `\MVF\Codeception\UseCases\Contracts\ActorInterface` like this

```php
<?php

/**
 * Inherited Methods
 *
 * @SuppressWarnings(PHPMD)
*/
class UseCaseTester extends \Codeception\Actor implements \MVF\Codeception\UseCases\Contracts\ActorInterface
{
    use _generated\UseCaseTesterActions;
}
```

you will also need to add the following method to your class in order for `ActorInterface` to be properly implemented.

```php
/**
 * Returns an array that describes what columns in the table uniquely identify rows of that table, e.g.
 *
 * If our list of $uniqueIdentifiers contains
 *
 * $uniqueIdentifiers = [
 *      SubcategoryTable::getTableName() => [SubcategoryTable::id()],
 *      PendingInvoiceBillingDataTable::getTableName() => [
 *          PendingInvoiceBillingDataTable::pendingInvoiceId(),
 *          PendingInvoiceBillingDataTable::billingDataId(),
 *      ],
 * ];
 *
 * Then
 *
 * $uniqueIdentifiers[SubcategoryTable::getTableName()];
 *
 * Would return ['sugarcrm.zz_subcategories.id'] which identifies that 'sugarcrm.zz_subcategories.id' column is
 * enough to uniquely identify 'sugarcrm.zz_subcategories' rows, on the other hand
 *
 * $uniqueIdentifiers[PendingInvoiceBillingDataTable::getTableName()];
 *
 * would return [
 *      'sugarcrm.pending_invoice_billing_data.pending_invoice_id',
 *      'sugarcrm.pending_invoice_billing_data.billing_data_id',
 * ]
 *
 * this would mean that both 'sugarcrm.pending_invoice_billing_data.pending_invoice_id' and
 * 'sugarcrm.pending_invoice_billing_data.billing_data_id' together can be used to uniquely identify rows in
 * 'sugarcrm.pending_invoice_billing_data' table.
 *
 * @param string $databaseAndTable
 * @return string[]
 */
public function getKeyIdentifier(string $databaseAndTable): array
{
    $uniqueIdentifiers = [];

    if (!isset($uniqueIdentifiers[$databaseAndTable])) {
        $className = get_class($this);
        throw new RuntimeException("'$databaseAndTable' does not have unique identifier defined, open '$className' and add it to the list of \$uniqueIdentifiers");
    }

    return $uniqueIdentifiers[$databaseAndTable];
}
```

This error should now be resolved.
