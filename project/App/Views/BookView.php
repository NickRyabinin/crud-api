<?php

namespace App;

class BookView
{
    public function send($data)
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        http_response_code($data['response_code']);
        echo json_encode($data['message']);
    }
}
