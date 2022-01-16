# CODECEPTION USE CASES MODULE

> A codeception module for use case based tests, this module aims to promote writing of real unit tests as described in the "TDD, Where Did It All Go Wrong" talk by Ian Cooper.

Relevant references:

* [TDD, Where Did It All Go Wrong](https://www.youtube.com/watch?v=EZ05e7EMOLM&ab_channel=DevTernityConference)
* [How to write use case tests](?)

## Setup

Assuming you have `tests/usecase.suite.yaml` file, enable this module like this
```yml
actor: UseCaseTester
modules:
    enabled:
        - \Helper\UseCase
        - \MVF\Codeception\UseCases\Module:
            providers:
                mysql: App\Providers\UseCases\EloquentProvider
                redis: App\Providers\UseCases\RedisProvider
        - Asserts
```

Specifically note `\MVF\Codeception\UseCases\Module` added to the list of modules. This module has required config to function properly, the list of `providers` must be defined.

### MySql Provider
This is an object that must implement `MVF\Codeception\UseCases\Contracts\MySqlInterface` and it should look something like this.

```php
namespace App\Providers\UseCases;

use Illuminate\Database\Connection;
use MVF\Codeception\UseCases\Contracts\MySqlInterface;

class EloquentProvider implements MySqlInterface
{
    public function __construct() {}

    public function getMySql(): Connection
    {
        return ...; // return writable eloquent connection, this connection should be privileged, able to write, read, delete and truncate tables. 
    }
}
```

### Redis Provider
This is an object that must implement `MVF\Codeception\UseCases\Contracts\RedisInterface` and it should look something like this.

```php
namespace App\Providers\UseCases;

use MVF\Codeception\UseCases\Contracts\RedisInterface;
use Redis;

class RedisProvider implements RedisInterface
{
    public function __construct() {}

    public function getRedis(): Redis
    {
        return ...; // return redis object
    }
}
```

### Services

Your docker compose file should have the following services
```yml
  <app>-test-mysql:
    container_name: <app>-test-mysql
    networks: [mvf_shared]
    image: mysql:5.7.26
    environment:
      MYSQL_ROOT_PASSWORD: 12345
      MYSQL_DATABASE: ...
      MYSQL_USER: ...
      MYSQL_PASSWORD: ...
    healthcheck:
      test: mysqladmin -uroot -p12345 ping -h localhost
      interval: 2s
      timeout: 20s
      retries: 10
    volumes:
      - mysql-test:/var/lib/mysql:cached
    command: --server-id=1 --log-bin=test.log

  <app>-binlog-parser:
    build:
      context: build/binlog-parser
    container_name: <app>-binlog-parser
    networks: [mvf_shared]
    restart: always

  <app>-redis:
    container_name: <app>-redis
    networks: [mvf_shared]
    image: redis:6-alpine

  networks:
    mvf_shared:
      external: true
```

#### \<app\>-test-mysql
MySql service should have `MYSQL_ROOT_PASSWORD` set to `12345`, have command with the following flags `--server-id=1 --log-bin=test.log`, if you already define some command just append these flags to your existing command, and should use `[mvf_shared]` network.

#### \<app\>-binlog-parser
Maxwell service should use `[mvf_shared]` network as well and it should be built in `build/binlog-parser` folder. this folder should have `Dockerfile` and `config.properties` files.

The content of your `Dockerfile` is below, check `maxwell` and `alpine` versions to see if there is a newer version available.
```dockerfile
FROM openjdk:18-ea-11-jdk-alpine3.15

RUN apk add --no-cache --update bash shadow
RUN /usr/sbin/groupadd -g 1000 www
RUN /usr/sbin/useradd -s /bin/sh -g 1000 -u 1000 www

ENV MAXWELL_VERSION=1.35.5

COPY --from=zendesk/maxwell:v1.35.5 /app /app

WORKDIR /app

RUN chown 1000:1000 /app && echo "$MAXWELL_VERSION" > /REVISION

USER 1000

COPY config.properties ./config.properties

CMD [ "/bin/bash", "-c", "bin/maxwell", "--config=/app/config.properties" ]
```

The content of your `config.properties` is below.
```apacheconf
log_level=info

# mysql source config
host=<app>-test-mysql
user=root
password=12345

# redis target config
producer=redis
redis_key=maxwell
redis_type=xadd
redis_host=<app>-redis
redis_port=6379
```

Most of this config should be self-explanatory the only thing to elaborate is `redis_key` this is where maxwell will store all the bin log data, by default this key is set to `maxwell` but if you are already using this key in your application then you need to change the value of it.

If you change your `redis_key` value to `testing` then you must also provide additional config in your test suite. Assuming you have `tests/usecase.suite.yaml` then new `redis_key` must also be defined there like this.
```yaml
actor: UseCaseTester
modules:
    enabled:
        - \MVF\Codeception\UseCases\Module:
            providers: ...
            redis_key: testing # <- this needs to match redis key in config.properties
```

#### \<app\>-redis
Redis service should use `[mvf_shared]` network like the other services.

## Requirements and Motivation

For this module to work you will need:

* MySql test database with binlogs enabled
* Maxwell daemon to read binlogs and push them to Redis
* Redis cache to store binlog data

There is quite a lot going on in this module, I assume you will have a lot of questions, lets start answering some of them.

### Why do we need mysql test server with bin logs enabled?
Short answer is, it is needed for maxwell daemon to know what mutations have been performs on the database. 

### What is Maxwell daemon?
This is a process that consumes mysql binlogs converts them to simple json objects and sends them to one of the supported targets, in our case a Redis server.

### Why does this module need Redis to function?
Again, short answer is, maxwell daemon uses redis as the store for binlogs. There are a number of different services where maxwell daemon can store binlogs but redis is the most suitable because we need to read the stored data in php code, and it is very easy to read data from Redis.

### What is the point of it all?

The added complexity solves an issue we had with database resets. The database that we are working with takes about 40 seconds to reset after each test. Maxwell daemon allows us to know what tables were effected anc with that we can truncate only those tables that must be truncated. This improved the speed of each test around 120 times.

This also provides us a way to write purely declarative tests, at no point in you test you have to interact with a database or write logic. The process of writing tests can be summarised with these steps: 

* declare `initial state` of the application
* run code under test
* declare assertions against the `final state` of the application

`initial state` is just an associative array that describes everything about the state of your application before code execution and `final state` is an associative array that describes everything about your application after code execution.
