<?php

/**
 * Класс View формирует JSON и выводит его клиенту.
 */

 namespace App\Views;

class View
{
    public function send(string $responseCode, array $message): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Authorization');
        header('Content-Type: application/json');
        http_response_code($responseCode);
        echo json_encode($message);
    }
}
