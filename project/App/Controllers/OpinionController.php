<?php

/**
 * Контроллер OpinionController - обрабатывает действия пользователя с сущностью 'Opinion'.
 * Вызывает соответствующие методы модели. На основе данных, полученных от модели,
 * формирует результат, передаваемый во View.
 */

namespace App\Controllers;

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
        try {
            $this->opinion->store($parentId, $token, $cleanInputData);
            parent::handleCreatedOk();
        } catch (InvalidIdException $e) {
            parent::handleResourceNotFound();
        } catch (InvalidTokenException $e) {
            parent::handleInvalidToken();
        } catch (InvalidDataException $e) {
            parent::handleInvalidData();
        }
    }

    public function read(): void
    {
        $page = $this->helper->getPage();
        [$parentId, $childId] = $this->getParams();
        match ($childId) {
            '' => parent::handleEmptyId($parentId, $page),
            false => parent::handleInvalidId(),
            default => parent::handleValidId($parentId, $childId)
        };
    }

    public function update(): void
    {
        [$parentId, $childId, $token, $cleanInputData] = $this->getParams();
        if ($childId === '' || $childId === false) {
            parent::handleInvalidId();
            return;
        }
        try {
            $this->opinion->update($parentId, $childId, $token, $cleanInputData);
            parent::handleUpdatedOk();
        } catch (InvalidIdException $e) {
            parent::handleResourceNotFound();
        } catch (InvalidTokenException $e) {
            parent::handleInvalidToken();
        } catch (InvalidDataException $e) {
            parent::handleInvalidData();
        }
    }

    public function delete(): void
    {
        [$parentId, $childId, $token] = $this->getParams();
        if ($childId === '' || $childId === false) {
            parent::handleInvalidId();
            return;
        }
        try {
            $this->opinion->destroy($parentId, $childId, $token);
            parent::handleDeletedOk();
        } catch (InvalidIdException $e) {
            parent::handleNoRecord();
        } catch (InvalidTokenException $e) {
            parent::handleInvalidToken();
        } catch (InvalidDataException $e) {
            parent::handleInvalidData();
        }
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
        $belongsTo = $this->opinion->belongsTo;
        if (!in_array(rtrim($parentResource, 's'), $belongsTo)) {
            parent::handleResourceNotFound();
            die();
        }
        if (!$parentId) {
            parent::handleInvalidId();
            die();
        }
    }
}
