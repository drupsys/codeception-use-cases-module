# MissingRequiredProvider

There are two providers that this module requires in order to be able to function, these are `mysql` and `redis` providers. If you receive this error that means you have not implemented one of these providers or incorrectly configured this module.

First, check suite configuration, assuming `\MVF\Codeception\UseCases\Module` is implemented by `usecase.suite.yml` then your suite should look something like this

```yaml
actor: UseCaseTester
modules:
    enabled:
        - \MVF\Codeception\UseCases\Module:
            providers:
                mysql: ... # Class that implements MVF\Codeception\UseCases\Contracts\MySqlInterface
                redis: ... # Class that implements MVF\Codeception\UseCases\Contracts\RedisInterface
```

Make sure the `providers` object is present and both `mysql` and  `redis` properties are defined. Make sure `yml` is correctly indented, incorrectly indented `yml` file can also cause this exception.
