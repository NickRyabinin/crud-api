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

    public function create(): void
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

    public function update(): void
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

    public function delete(): void
    {
        $id = $this->helper->getId();
        $token = $this->helper->getToken();
        if ($id === '' || $id === false) {
            parent::handleInvalidId();
            return;
        }
        try {
            $this->book->destroy($id, $token);
            parent::handleDeletedOk();
        } catch (InvalidIdException $e) {
            parent::handleNoRecord();
        } catch (InvalidTokenException $e) {
            parent::handleInvalidToken();
        } catch (InvalidDataException $e) {
            parent::handleInvalidData();
        }
    }
}
