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

    public function read(): void
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

    private function handleEmptyId(): void
    {
        $message = $this->model->index();
        if ($message === []) {
            $this->handleNoRecords();
            return;
        }
        $this->handleOk($message);
    }

    private function handleValidId(string $id): void
    {
        $message = $this->model->show($id);
        if ($message === false) {
            $this->handleNoRecord();
            return;
        }
        $this->handleOk($message);
    }

    public function handleInvalidId(): void
    {
        $responseCode = '400';
        $message = ['error' => 'Invalid ID'];
        $this->view->send($responseCode, $message);
    }

    public function handleInvalidMethod(): void
    {
        $responseCode = '405';
        $message = ['error' => 'Method not allowed'];
        $this->view->send($responseCode, $message);
    }

    public function handleInvalidData(): void
    {
        $responseCode = '400';
        $message = ['error' => 'Invalid input data'];
        $this->view->send($responseCode, $message);
    }

    public function handleInvalidToken(): void
    {
        $responseCode = '401';
        $message = ['error' => 'Unauthorized, no such token'];
        $this->view->send($responseCode, $message);
    }

    public function handleNoRecord(): void
    {
        $responseCode = '404';
        $message = ['error' => 'No record with such ID'];
        $this->view->send($responseCode, $message);
    }

    public function handleNoRecords(): void
    {
        $responseCode = '404';
        $message = ['error' => 'No records'];
        $this->view->send($responseCode, $message);
    }

    public function handleOk(array $message): void
    {
        $responseCode = '200';
        $this->view->send($responseCode, $message);
    }

    public function handleCreatedOk(): void
    {
        $responseCode = '201';
        $message = ['message' => "Done, {$this->model} added successfully"];
        $this->view->send($responseCode, $message);
    }

    public function handleUpdatedOk(): void
    {
        $responseCode = '200';
        $message = ['message' => "Done, {$this->model} updated successfully"];
        $this->view->send($responseCode, $message);
    }
}
