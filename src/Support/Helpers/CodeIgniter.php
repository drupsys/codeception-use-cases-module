<?php

namespace MVF\UseCases\Support\Helpers;

use Scheb\Tombstone\Logger\Graveyard\GraveyardBuilder;

class CodeIgniter extends \Codeception\Module
{
    private $ci;

    public function _initialize()
    {
        require_once(dirname(__FILE__) . '/../CodeIgniter/Setup.php');
        $this->ci = get_instance();

        require_once('vendor/scheb/tombstone-logger/tombstone-function.php');
        $graveyard = new GraveyardBuilder();
        $graveyard->rootDirectory(__DIR__);
        $graveyard->autoRegister();
        $graveyard->build();
    }

    public function getCodeIgniterInstance()
    {
        return $this->ci;
    }
}
