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
            parent::handleInvalidData();
            return;
        }
        $inputData = $this->helper->getInputData();
        $cleanData = array_map(fn ($param) => $this->helper->sanitize($this->helper->validate($param)), $inputData);
        try {
            $this->book->store($token, $cleanData);
            parent::handleCreatedOk();
        } catch (InvalidTokenException $e) {
            parent::handleInvalidToken();
        } catch (InvalidDataException $e) {
            parent::handleInvalidData();
        }
    }

    public function update()
    {
        $id = $this->helper->getId();
        $token = $this->helper->getToken();
        if ($id === '' || $id === false) {
            parent::handleInvalidId();
            return;
        }
        $inputData = $this->helper->getInputData();
        $cleanData = array_map(fn ($param) => $this->helper->sanitize($this->helper->validate($param)), $inputData);
        try {
            $this->book->update($id, $token, $cleanData);
            parent::handleUpdatedOk();
        } catch (InvalidIdException $e) {
            parent::handleNoRecord();
        } catch (InvalidTokenException $e) {
            parent::handleInvalidToken();
        } catch (InvalidDataException $e) {
            parent::handleInvalidData();
        }
    }

    public function delete()
    {
        $id = $this->helper->getId();
        $token = $this->helper->getToken();
        if ($id === '' || $id === false) {
            $responseCode = '400';
            $message = ['error' => 'Invalid ID'];
        } else {
            try {
                $message = $this->book->destroy($id, $token);
                $responseCode = '200';
                $message = ['message' => "Done, book deleted successfully"];
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
}
