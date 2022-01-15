<?php

namespace MVF\Codeception\UseCases\Contracts;

use Illuminate\Database\Connection;

interface MySqlInterface
{
    public function __construct();
    public function getMySql(): Connection;
}
