<?php

namespace App\Controllers;

abstract class Controller
{
    use MessagesHandlers;

    protected $model;
    // protected $view;
    // protected $helper;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function read(): void
    {
        $page = $this->helper->getPage();
        $id = $this->helper->getId();
        match ($id) {
            '' => $this->handleEmptyId(page: $page),
            false => $this->handleInvalidId(),
            default => $this->handleValidId($id)
        };
    }
}
