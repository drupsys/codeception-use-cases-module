# InvalidProviderImplementation

You will receive this error if one of the required providers does not implement appropriate interface.

## MySql Provider

If you receive
```text
Provided 'App\YourObject' does not implement 'MVF\Codeception\UseCases\Contracts\MySqlInterface'
```

Then you need to open `App\YourObject` and implement `MVF\Codeception\UseCases\Contracts\MySqlInterface` like this

```php
namespace App;

use Illuminate\Database\Connection;
use MVF\Codeception\UseCases\Contracts\MySqlInterface;

class YourObject implements MySqlInterface
{
    public function __construct() {}

    public function getMySql(): Connection
    {
        return ...; // return writable eloquent connection
    }
}
```

## Redis Provider

If you receive
```text
Provided 'App\YourObject' does not implement 'MVF\Codeception\UseCases\Contracts\RedisInterface'
```

Then you need to open `App\YourObject` and implement `MVF\Codeception\UseCases\Contracts\RedisInterface` like this

```php
namespace App;

use MVF\Codeception\UseCases\Contracts\RedisInterface;
use Redis;

class YourObject implements RedisInterface
{
    public function __construct() {}

    public function getRedis(): Redis
    {
        return ...; // return redis object
    }
}
```
