<?php

namespace App\Routers;

use App\Core\Container;

class Router
{
    private Container $container;
    private object $helper;

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
        try {
            $controller = $this->getController($resource);
        } catch (\Exception $e) {
            $this->container->get('exceptionController')->handleException($e);
            return;
        }
        $method = $this->helper->getHttpMethod();

        $this->handleRequestMethod($controller, $method);
    }

    private function handleRequestMethod(object $controller, string $method): void
    {
        match ($method) {
            'GET' => $controller->read(),
            'POST' => $controller->create(),
            'PUT', 'PATCH' => $controller->update(),
            'DELETE' => $controller->delete(),
            default => $controller->handleInvalidMethod()
        };
    }

    private function getController(string $resource): object
    {
        $controllerName = $this->helper->sanitize(substr($resource, 0, -1)) . 'Controller';

        if ($this->container->get($controllerName) !== false) {
            return $this->container->get($controllerName);
        }
        throw new \Exception("Controller for resource '{$resource}' not found", 404);
    }
}
