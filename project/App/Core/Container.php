<?php

namespace App\Core;

class Container
{
    private $services = [];

    public function set(string $name, object $service): void
    {
        $this->services[$name] = $service;
    }

    public function get(string $name): object | bool
    {
        if (isset($this->services[$name])) {
            if (is_callable($this->services[$name])) {
                return call_user_func($this->services[$name], $this);
            }
            return $this->services[$name];
        }
        return false;
    }
}
