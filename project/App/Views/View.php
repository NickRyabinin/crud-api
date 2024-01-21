<?php

namespace App\Views;

class View
{
    public function send(string $responseCode, array $message): void
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        http_response_code($responseCode);
        echo json_encode($message);
    }
}
