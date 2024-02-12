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
            $this->handleNoRecords();
        }
        $this->handleOk($message);
    }

    private function handleValidId($id)
    {
        $message = $this->model->show($id);
        if ($message === false) {
            $this->handleNoRecord();
        }
        $this->handleOk($message);
    }

    public function handleInvalidId()
    {
        $responseCode = '400';
        $message = ['error' => 'Invalid ID'];
        $this->view->send($responseCode, $message);
    }

    public function handleInvalidMethod()
    {
        $responseCode = '405';
        $message = ['error' => 'Method not allowed'];
        $this->view->send($responseCode, $message);
    }

    public function handleInvalidData()
    {
        $responseCode = '400';
        $message = ['error' => 'Invalid input data'];
        $this->view->send($responseCode, $message);
    }

    public function handleInvalidToken()
    {
        $responseCode = '401';
        $message = ['error' => 'Unauthorized, no such token'];
        $this->view->send($responseCode, $message);
    }

    public function handleNoRecord()
    {
        $responseCode = '404';
        $message = ['error' => 'No record with such ID'];
        $this->view->send($responseCode, $message);
    }

    public function handleNoRecords()
    {
        $responseCode = '404';
        $message = ['error' => 'No records'];
        $this->view->send($responseCode, $message);
    }

    public function handleOk($message)
    {
        $responseCode = '200';
        $this->view->send($responseCode, $message);
    }
}
