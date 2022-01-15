<?php


namespace MVF\Codeception\UseCases;

use Illuminate\Database\Connection;
use MVF\Codeception\UseCases\Contracts\MySqlInterface;
use MVF\Codeception\UseCases\Contracts\RedisInterface;
use MVF\Codeception\UseCases\Exceptions\InvalidConnectionObject;
use MVF\Codeception\UseCases\Exceptions\MissingRequiredConfig;
use Redis;

class Config
{
    private static array $config = [];

    private static ?Connection $mysql = null;
    private static ?Redis $redis = null;

    public static function set(array $config)
    {
        self::$config = $config;
    }

    public static function mysql(): Connection
    {
        if (self::$mysql === null) {
            $className = self::$config['mysql'];
            if (empty($className)) {
                throw new MissingRequiredConfig('mysql');
            }

            $instance = new $className();
            if ($instance instanceof MySqlInterface) {
                self::$mysql = $instance->getMySql();
            } else {
                throw new InvalidConnectionObject("MYSQL");
            }
        }

        return self::$mysql;
    }

    public static function redis(): Redis
    {
        if (self::$redis === null) {
            $className = self::$config['redis'];
            if (empty($className)) {
                throw new MissingRequiredConfig('redis');
            }

            $instance = new $className();
            if ($instance instanceof RedisInterface) {
                self::$redis = $instance->getRedis();
            } else {
                throw new InvalidConnectionObject("REDIS");
            }
        }

        return self::$redis;
    }
}
