<?php

namespace MVF\UseCases;

class Module extends \Codeception\Module
{
    public function _initialize()
    {
        Config::set($this->config);
    }
}
