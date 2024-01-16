<?php

namespace App\Routers;

use App\Core\Container;
use App\Core\Helper;

class BookRouter
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
        $controller = $this->getController();
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

    private function getController()
    {
        $resource = $this->helper->getResource();
        $controllerName = $this->helper->sanitize(substr($resource, 0, -1)) . 'Controller';

        if ($this->container->get($controllerName) !== false) {
            return $this->container->get($controllerName);
        } else {
            die("I'm dying...");
        }
    }
}
