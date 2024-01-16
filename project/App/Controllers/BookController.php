<?php

namespace App\Controllers;

use App\Models\Book;
use App\Views\BookView;
use App\Core\Helper;

class BookController
{
    private $book;
    private $view;
    private $helper;

    public function __construct(Book $book, BookView $view, Helper $helper)
    {
        $this->book = $book;
        $this->view = $view;
        $this->helper = $helper;
    }

    public function read()
    {
        $id = $this->helper->getId();
        if ($id === '') {
            $message = $this->book->getAll();
            if ($message === []) {
                $responseCode = '404';
                $message = ['error' => 'No records'];
            } else {
                $responseCode = '200';
            }
        } elseif ($id === false) {
            $responseCode = '400';
            $message = ['error' => 'Invalid ID'];
        } else {
            $message = $this->book->get($id);
            if ($message === false) {
                $responseCode = '404';
                $message = ['error' => 'No record with such ID'];
            } else {
                $responseCode = '200';
            }
        }
        $this->view->send($responseCode, $message);
    }

    public function delete()
    {
        $id = $this->helper->getId();
        if ($id === '' || $id === false) {
            $responseCode = '400';
            $message = ['error' => 'Invalid ID'];
        } else {
            $message = $this->book->destroy($id);
            if ($message === false) {
                $responseCode = '404';
                $message = ['error' => 'No record with such ID'];
            } else {
                $responseCode = '200';
                $message = ["Done, book deleted successfully"];
            }
        }
        $this->view->send($responseCode, $message);
    }

    public function invalidMethod()
    {
        $responseCode = '405';
        $message = ['error' => 'Method not allowed'];
        $this->view->send($responseCode, $message);
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
