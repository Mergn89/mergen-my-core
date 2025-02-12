<?php

namespace Core;

use Controller\CartController;
use Controller\OrderController;
use Controller\ProductController;
use Controller\UserController;
use Service\Auth\AuthSessionService;
use Service\CartService;
use Service\OrderService;

class Container
{
    private array $services = [];

    public function get(string $class): object
    {

        if(!isset($this->services[$class])) {
            return new $class();
        }

        $callback = $this->services[$class];
        return $callback($this);

    }

    public function set(string $class, callable $callback): void
    {
        $this->services[$class] = $callback;

    }

}