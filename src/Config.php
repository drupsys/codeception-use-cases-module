<?php


namespace MVF\UseCases;

use Illuminate\Database\Connection;
use MVF\Exceptions\InvalidConnectionObject;
use MVF\Exceptions\MissingRequiredConfig;
use MVF\UseCases\Contracts\MySqlInterface;
use MVF\UseCases\Contracts\RedisInterface;
use Redis;

class Config
{
    private static array $config;

    private static ?Connection $mysql;
    private static ?Redis $redis;

    public static function set(array $config)
    {
        self::$config = $config;
    }

    public static function mysql(): Connection
    {
        if (!isset(self::$mysql)) {
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
        if (!isset(self::$redis)) {
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
