<?php


namespace MVF\Codeception\UseCases;

use Illuminate\Database\Connection;
use MVF\Codeception\UseCases\Contracts\MySqlInterface;
use MVF\Codeception\UseCases\Contracts\RedisInterface;
use MVF\Codeception\UseCases\Exceptions\InvalidProviderImplementation;
use MVF\Codeception\UseCases\Exceptions\MissingRequiredProvider;
use Redis;

class Config
{
    private static array $config = [];

    private static ?Connection $mysql = null;
    private static ?Redis $redis = null;

    public static function set(array $config)
    {
        self::$config = $config;

        if (!isset(self::$config['providers']['mysql']) || empty(self::$config['providers']['mysql'])) {
            throw new MissingRequiredProvider('mysql');
        }

        if (!isset(self::$config['providers']['redis']) || empty(self::$config['providers']['redis'])) {
            throw new MissingRequiredProvider('redis');
        }
    }

    public static function mysql(): Connection
    {
        if (self::$mysql === null) {
            $className = self::$config['providers']['mysql'];
            $instance = new $className();
            if ($instance instanceof MySqlInterface) {
                self::$mysql = $instance->getMySql();
            } else {
                throw new InvalidProviderImplementation($className, MySqlInterface::class);
            }
        }

        return self::$mysql;
    }

    public static function redis(): Redis
    {
        if (self::$redis === null) {
            $className = self::$config['providers']['redis'];
            $instance = new $className();
            if ($instance instanceof RedisInterface) {
                self::$redis = $instance->getRedis();
            } else {
                throw new InvalidProviderImplementation($className, RedisInterface::class);
            }
        }

        return self::$redis;
    }
}
