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
    protected $view;
    protected $helper;

    public function __construct(Opinion $opinion, View $view, Helper $helper)
    {
        parent::__construct($opinion);
        $this->opinion = $opinion;
        $this->view = $view;
        $this->helper = $helper;
    }

    public function create(): void
    {
        [$parentId, $childId, $token, $cleanInputData] = $this->getParams();
        if ($childId !== '') {
            parent::handleInvalidData();
            return;
        }
        $cleanInputData['book_id'] = $parentId;
        try {
            $this->opinion->store($token, $cleanInputData);
            parent::handleCreatedOk();
        } catch (InvalidTokenException $e) {
            parent::handleInvalidToken();
        } catch (InvalidDataException $e) {
            parent::handleInvalidData();
        }
    }

    public function read(): void
    {
        [$parentId, $childId] = $this->getParams();
        match ($childId) {
            '' => parent::handleEmptyId($parentId),
            false => parent::handleInvalidId(),
            default => parent::handleValidId($parentId, $childId)
        };
    }

    protected function getParams(): array
    {
        $parentResource = $this->helper->getResource('parent');
        $parentId = $this->helper->getId();
        $this->checkParentResource($parentResource, $parentId);
        $childId = $this->helper->getId('nested');
        $token = $this->helper->getToken();
        $inputData = $this->helper->getInputData();
        $cleanData = array_map(fn ($param) => $this->helper->sanitize($this->helper->validate($param)), $inputData);
        return [$parentId, $childId, $token, $cleanData];
    }

    protected function checkParentResource(string $parentResource, string $parentId): void
    {
        if ($parentResource !== 'books') {
            parent::handleResourceNotFound();
            die();
        }
        if (!$parentId) {
            parent::handleInvalidId();
            die();
        }
    }
}
