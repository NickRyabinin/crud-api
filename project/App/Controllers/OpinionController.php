<?php

namespace App\Controllers;

use App\Models\Book;
use APP\Models\User;
use App\Views\View;
use App\Core\Helper;
use App\Core\Exceptions\InvalidIdException;
use App\Core\Exceptions\InvalidTokenException;
use App\Core\Exceptions\InvalidDataException;
use App\Models\Opinion;

class OpinionController extends Controller
{
    protected $opinion;
    protected $book;
    protected $user;
    protected $view;
    protected $helper;

    public function __construct(Opinion $opinion, Book $book, User $user, View $view, Helper $helper)
    {
        parent::__construct($opinion);
        $this->opinion = $opinion;
        $this->book = $book;
        $this->user = $user;
        $this->view = $view;
        $this->helper = $helper;
    }

    public function create(): void
    {
        $parentId = $this->helper->getId();
        $childId = $this->helper->getId('nested');
        $token = $this->helper->getToken();
        if ($childId !== '' && !$parentId) {
            parent::handleInvalidData();
            return;
        }
        $inputData = $this->helper->getInputData();
        $cleanData = array_map(fn ($param) => $this->helper->sanitize($this->helper->validate($param)), $inputData);
        $cleanData['book_id'] = $parentId;
        try {
            $this->opinion->store($token, $cleanData);
            parent::handleCreatedOk();
        } catch (InvalidTokenException $e) {
            parent::handleInvalidToken();
        } catch (InvalidDataException $e) {
            parent::handleInvalidData();
        }
    }
}
