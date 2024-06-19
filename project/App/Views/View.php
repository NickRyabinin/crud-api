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
        header('Content-Type: application/json');
        http_response_code($responseCode);
        echo json_encode($message);
    }
}
