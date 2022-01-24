# StageDoesNotHaveAnOperationName

You will see this exception if any of your stages do not have an `operation` name, e.g.

you have something like this
```php
'inputs' => [
    'state' => [
        'stages' => [
            [
                'request' => [],
            ],
            ...
        ],
        ...
    ],
    ...
],
```

instead of

```php
'inputs' => [
    'state' => [
        'stages' => [
            [
                'operation' => 'SET_NOW',
                'request' => [],
            ],
            ...
        ],
        ...
    ],
    ...
],
```
