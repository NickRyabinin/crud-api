<?php

namespace App\Routers;

use App\Core\Container;

class Router
{
    private Container $container;
    private $helper;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->helper = $this->container->get('helper');
    }

    public function route(): void
    {
        $resource = $this->helper->getResource();
        if ($resource === '') {
            $this->container->get('homeController')->index();
            return;
        }
        $controller = $this->getController($resource);
        $method = $this->helper->getHttpMethod();

        $this->handleRequestMethod($controller, $method);
    }

    private function handleRequestMethod($controller, string $method): void
    {
        match ($method) {
            'GET' => $controller->read(),
            'POST' => $controller->create(),
            'PUT', 'PATCH' => $controller->update(),
            'DELETE' => $controller->delete(),
            default => $controller->handleInvalidMethod()
        };
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
