<?php

namespace books;

class BookView
{
    public function send($data)
    {
        header('Content-Type: application/json');
        http_response_code($data['response_code']);
        echo json_encode($data['message']);
    }
}

/* http_response_code(400);
['error' => 'Invalid input data']

http_response_code(405);
['error' => 'Method not allowed']

http_response_code(404);
['error' => 'No record with such ID']

['message' => 'No records']

http_response_code(201);
['message' => "Done, {$entity} added successfully"]

['message' => "Done, {$entity} updated successfully"]
['message' => "Done, {$entity} patched successfully"]
['message' => "Done, {$entity} deleted successfully"] */
