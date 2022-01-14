<?php

namespace MVF\UseCases\Support\Helpers;

use Codeception\Module;
use MVF\UseCases\Config;

class UseCases extends Module
{
    public function _initialize()
    {
        Config::set($this->config);
    }
}
