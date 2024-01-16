<?php

namespace App\Core;

class Container
{
    private $services = [];

    public function set($name, $service)
    {
        $this->services[$name] = $service;
    }

    public function get($name)
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
