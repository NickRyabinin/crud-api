<?php

/**
 * Класс View формирует JSON и выводит его клиенту.
 */

 namespace App\Views;

class View
{
    public function send(string $responseCode, array $message): void
    {
        header('Content-Type: application/json');
        /*header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Authorization'); */
        http_response_code($responseCode);
        echo json_encode($message);
    }
}
