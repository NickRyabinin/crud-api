<?php

namespace App\Routers;

use App\Core\Container;

class Router
{
    private $container;
    private $helper;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->helper = $this->container->get('helper');
    }

    public function route()
    {
        $resource = $this->helper->getResource();
        if ($resource === '') {
            $controller = $this->container->get('homeController');
            $controller->index();
            return;
        }
        $controller = $this->getController($resource);
        $method = $this->helper->getHttpMethod();

        switch ($method) {
            case 'GET':
                $controller->read();
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

    private function getController(string $resource)
    {
        $controllerName = $this->helper->sanitize(substr($resource, 0, -1)) . 'Controller';

        if ($this->container->get($controllerName) !== false) {
            return $this->container->get($controllerName);
        }
        die("Resource not found");
    }
}
