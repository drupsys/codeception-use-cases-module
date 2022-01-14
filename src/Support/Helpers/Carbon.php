<?php

namespace MVF\UseCases\Support\Helpers;

use Codeception\TestInterface;
use Carbon\Carbon as CarbonTime;
use DateTime;

class Carbon extends \Codeception\Module
{
    public function _before(TestInterface $test)
    {
        CarbonTime::setTestNow(CarbonTime::instance(new DateTime('01-01-2000 00:00:00')));
    }

    public function _after(TestInterface $test)
    {
        CarbonTime::setTestNow();
    }
}
