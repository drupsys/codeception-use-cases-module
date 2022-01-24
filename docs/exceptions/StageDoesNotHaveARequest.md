# StageDoesNotHaveARequest

You will see this exception if any of your stages do not have an `request` provided, e.g.

you have something like this
```php
'inputs' => [
    'state' => [
        'stages' => [
            [
                'operation' => 'SET_NOW',
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
