<?php

namespace App\Views;

class HomeView
{
    public function render(): void
    {
        header('Content-Type: text/html');
        header('Access-Control-Allow-Origin: *');
        http_response_code('200');
        echo file_get_contents(__DIR__ . '/../../www/index.html');
    }
}
