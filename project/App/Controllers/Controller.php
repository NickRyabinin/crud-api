<?php

namespace App\Controllers;

abstract class Controller
{
    protected $model;
    protected $view;
    protected $helper;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function read()
    {
        $id = $this->helper->getId();
        switch ($id) {
            case '':
                $this->handleEmptyId();
                break;
            case false:
                $this->handleInvalidId();
                break;
            default:
                $this->handleValidId($id);
                break;
        }
    }

    private function handleEmptyId()
    {
        $message = $this->model->index();
        if ($message === []) {
            $responseCode = '404';
            $message = ['error' => 'No records'];
        } else {
            $responseCode = '200';
        }
        $this->view->send($responseCode, $message);
    }

    private function handleInvalidId()
    {
        $responseCode = '400';
        $message = ['error' => 'Invalid ID'];
        $this->view->send($responseCode, $message);
    }

    private function handleValidId($id)
    {
        $message = $this->model->show($id);
        if ($message === false) {
            $responseCode = '404';
            $message = ['error' => 'No record with such ID'];
        } else {
            $responseCode = '200';
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
