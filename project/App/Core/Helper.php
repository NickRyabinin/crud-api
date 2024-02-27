<?php

namespace App\Core;

class Helper
{
    public function getHttpMethod(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    public function getId(string $nested = ''): string | bool
    {
        $request = $this->getRequest();
        if ($nested) {
            $id = $request[3] ?? '';
        } else {
            $id = $request[1] ?? '';
        }
        if ($id !== '') {
            return (preg_match('/^\d+$/', $id)) ? (string)$id : false;
        }
        return '';
    }

    public function getRequest(): array
    {
        return explode('/', trim($_SERVER['REQUEST_URI'], '/'));
    }

    public function getResource(): string
    {
        $resource = isset($this->getRequest()[2]) ? $this->getRequest()[2] : $this->getRequest()[0];
        return $this->sanitize($this->validate($resource));
    }

    public function getInputData(): array
    {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }

    public function getToken(): string
    {
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            $bearerToken = explode(' ', $authHeader);
            $token = $bearerToken[1];
            return $token;
        }
        return '';
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
