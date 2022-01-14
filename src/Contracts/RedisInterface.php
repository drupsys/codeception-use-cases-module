<?php

namespace MVF\UseCases\Contracts;

use Redis;

interface RedisInterface
{
    public function __construct();
    public function getRedis(): Redis;
}
