<?php

namespace App\Controllers;

use App\Models\Book;
use App\Views\BookView;

class BookController
{
    private $book;
    private $view;

    public function __construct(Book $book, BookView $view)
    {
        $this->book = $book;
        $this->view = $view;
    }

    public function readAll()
    {
        $message = $this->book->getAll();
        $responseCode = '200';
        $data = ['response_code' => $responseCode, 'message' => $message];
        $this->view->send($data);
    }

    public function invalidMethod()
    {
        $data = ['response_code' => '405', 'message' => ['error' => 'Method not allowed']];
        $this->view->send($data);
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
