<?php

namespace App\Controllers;

use App\Models\Book;
use App\Views\View;
use App\Core\Helper;
use App\Core\Exceptions\InvalidIdException;
use App\Core\Exceptions\InvalidTokenException;
use App\Core\Exceptions\InvalidDataException;

class BookController extends Controller
{
    protected $book;
    protected $view;
    protected $helper;

    public function __construct(Book $book, View $view, Helper $helper)
    {
        parent::__construct($book);
        $this->book = $book;
        $this->view = $view;
        $this->helper = $helper;
    }

    public function create()
    {
        $id = $this->helper->getId();
        $token = $this->helper->getToken();
        if ($id !== '') {
            $responseCode = '400';
            $message = ['error' => 'Invalid input data'];
        } else {
            $inputData = $this->helper->getInputData();
            $cleanData = array_map(fn ($param) => $this->helper->sanitize($this->helper->validate($param)), $inputData);
            try {
                $message = $this->book->store($token, $cleanData);
                $responseCode = '201';
                $message = ['message' => "Done, book added successfully"];
            } catch (InvalidTokenException $e) {
                $responseCode = '401';
                $message = ['error' => 'Unauthorized, no such token'];
            } catch (InvalidDataException $e) {
                $responseCode = '400';
                $message = ['error' => 'Invalid input data'];
            }
        }
        $this->view->send($responseCode, $message);
    }

    public function update()
    {
        $id = $this->helper->getId();
        $token = $this->helper->getToken();
        if ($id === '' || $id === false) {
            $responseCode = '400';
            $message = ['error' => 'Invalid ID'];
        } else {
            $inputData = $this->helper->getInputData();
            $cleanData = array_map(fn ($param) => $this->helper->sanitize($this->helper->validate($param)), $inputData);
            try {
                $message = $this->book->update($id, $token, $cleanData);
                $responseCode = '200';
                $message = ["Done, book updated successfully"];
            } catch (InvalidIdException $e) {
                $responseCode = '404';
                $message = ['error' => 'No record with such ID'];
            } catch (InvalidTokenException $e) {
                $responseCode = '401';
                $message = ['error' => 'Unauthorized, no such token'];
            } catch (InvalidDataException $e) {
                $responseCode = '400';
                $message = ['error' => 'Invalid input data'];
            }
        }
        $this->view->send($responseCode, $message);
    }

    public function delete()
    {
        $id = $this->helper->getId();
        $token = $this->helper->getToken();
        if ($id === '' || $id === false) {
            $responseCode = '400';
            $message = ['error' => 'Invalid ID'];
        } else {
            $message = $this->book->destroy($id, $token);
            if ($message === false) {
                $responseCode = '404';
                $message = ['error' => 'No record with such ID'];
            } elseif ($message === '') {
                $responseCode = '401';
                $message = ['error' => 'Unauthorized, no such token'];
            } else {
                $responseCode = '200';
                $message = ["Done, book deleted successfully"];
            }
        }
        $this->view->send($responseCode, $message);
    }
}
