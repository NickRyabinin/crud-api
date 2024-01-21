<?php

namespace App\Controllers;

use App\Models\Book;
use App\Views\View;
use App\Core\Helper;

class BookController
{
    private $book;
    private $view;
    private $helper;

    public function __construct(Book $book, View $view, Helper $helper)
    {
        $this->book = $book;
        $this->view = $view;
        $this->helper = $helper;
    }

    public function create()
    {
        $id = $this->helper->getId();
        $responseCode = '400';
        $message = ['error' => 'Invalid input data'];
        if ($id === '') {
            $inputData = $this->helper->getInputData();
            $cleanData = array_map(fn ($param) => $this->helper->sanitize($this->helper->validate($param)), $inputData);
            if ($this->book->store($cleanData)) {
                $responseCode = '201';
                $message = ['message' => "Done, book added successfully"];
            }
        }
        $this->view->send($responseCode, $message);
    }

    public function read()
    {
        $id = $this->helper->getId();
        if ($id === '') {
            $message = $this->book->index();
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
            $message = $this->book->show($id);
            if ($message === false) {
                $responseCode = '404';
                $message = ['error' => 'No record with such ID'];
            } else {
                $responseCode = '200';
            }
        }
        $this->view->send($responseCode, $message);
    }

    public function update()
    {
        $id = $this->helper->getId();
        if ($id === '' || $id === false) {
            $responseCode = '400';
            $message = ['error' => 'Invalid ID'];
        } else {
            $inputData = $this->helper->getInputData();
            $cleanData = array_map(fn ($param) => $this->helper->sanitize($this->helper->validate($param)), $inputData);
            $message = $this->book->update($id, $cleanData);
            if ($message === false) {
                $responseCode = '404';
                $message = ['error' => 'No record with such ID'];
            } elseif ($message === null) {
                $responseCode = '400';
                $message = ['error' => 'Invalid input data'];
            } else {
                $responseCode = '200';
                $message = ["Done, book updated successfully"];
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
['error' => 'Invalid ID']

http_response_code(405);
['error' => 'Method not allowed']

http_response_code(404);
['error' => 'No record with such ID']
['error' => 'No records']

http_response_code(201);
['message' => "Done, {$entity} added successfully"]

http_response_code(200);
['message' => "Done, {$entity} updated successfully"]
['message' => "Done, {$entity} deleted successfully"] */
