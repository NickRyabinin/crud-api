<?php

namespace App\Routers;

use App\Core\Container;

class BookRouter
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function route($method, $request)
    {
        $resource = $request[0];
        $controllerName = htmlspecialchars(substr($resource, 0, -1)) . 'Controller';

        if ($this->container->get($controllerName) !== false) {
            $controller = $this->container->get($controllerName);
        } else {
            die();
        }

        switch ($method) {
            case 'GET':
                empty($request[1]) ? $controller->readAll() : $controller->read();
                break;
            case 'POST':
                $controller->create();
                break;
            case 'PUT':
            case 'PATCH':
                $controller->update();
                break;
            case 'DELETE':
                $controller->delete();
                break;
            default:
                // Invalid method
                $controller->invalidMethod();
                break;
        }
    }
}
