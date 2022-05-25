<?php

namespace MVF\Codeception\UseCases;

use MVF\Servicer\Contracts\Action;

class GenericAction implements Action
{
    /**
     * @var callable
     */
    private $handler;
    private array $request = [];

    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    public function getRequest(): array
    {
        return $this->request;
    }

    public function handler(array $request): array
    {
        $this->request = $request;
        return ($this->handler)($request);
    }
}
