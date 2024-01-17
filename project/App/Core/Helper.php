<?php

namespace App\Core;

class Helper
{
    public function getHttpMethod(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    public function getId(): string | bool
    {
        $request = $this->getRequest();
        if (!empty($request[1])) {
            $id = $this->sanitize($this->validate($request[1]));
            return (is_numeric($id) && $id >= 0 && floor($id) == $id) ? $id : false;
        }
        return '';
    }

    public function getRequest(): array
    {
        return explode('/', trim($_SERVER['REQUEST_URI'], '/'));
    }

    public function getResource(): string
    {
        return $this->sanitize($this->validate($this->getRequest()[0]));
    }

    public function getInputData()
    {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }

    public function validate(mixed $data): string | bool
    {
        if (is_int($data) || is_string($data)) {
            return $data;
        }
        return false;
    }

    public function sanitize(string $data): string
    {
        return htmlspecialchars(strip_tags($data));
    }
}
