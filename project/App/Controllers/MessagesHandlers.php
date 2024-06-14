<?php

/**
 * Трейт MessagesHandlers - содержит обработчики, передающие во View для вывода пользователю
 * определённый код ответа и сообщение, в зависимости от логики контроллера, обрабатывающего
 * действия пользователя.
 */

namespace App\Controllers;

trait MessagesHandlers
{
    protected function handleEmptyId(string $parentId = '', string $page = '1'): void
    {
        $message = $this->model->index($parentId, $page);
        if ($message === []) {
            $this->handleNoRecords();
            return;
        }
        $this->handleOk($message);
    }

    protected function handleValidId(string $parentId, string $childId = ''): void
    {
        $message = $this->model->show($parentId, $childId);
        if ($message[0] === false) {
            $this->handleNoRecord();
            return;
        }
        $this->handleOk($message);
    }

    protected function handleInvalidId(): void
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

    protected function handleInvalidData(): void
    {
        $responseCode = '400';
        $message = ['error' => 'Invalid input data'];
        $this->view->send($responseCode, $message);
    }

    protected function handleInvalidToken(): void
    {
        $responseCode = '401';
        $message = ['error' => 'Unauthorized, no such token'];
        $this->view->send($responseCode, $message);
    }

    protected function handleNoRecord(): void
    {
        $responseCode = '404';
        $message = ['error' => 'No record with such ID'];
        $this->view->send($responseCode, $message);
    }

    protected function handleNoRecords(): void
    {
        $responseCode = '404';
        $message = ['error' => 'No records'];
        $this->view->send($responseCode, $message);
    }

    protected function handleOk(array $message): void
    {
        $responseCode = '200';
        $this->view->send($responseCode, $message);
    }

    protected function handleCreatedOk(): void
    {
        $responseCode = '201';
        $message = ['message' => "Done, {$this->model} added successfully"];
        $this->view->send($responseCode, $message);
    }

    protected function handleUserCreatedOk(string $token): void
    {
        $responseCode = '201';
        $message = [
            'message' => "Done, user added successfully",
            'token' => base64_encode($token)
        ];
        $this->view->send($responseCode, $message);
    }

    protected function handleUpdatedOk(): void
    {
        $responseCode = '200';
        $message = ['message' => "Done, {$this->model} updated successfully"];
        $this->view->send($responseCode, $message);
    }

    protected function handleDeletedOk(): void
    {
        $responseCode = '200';
        $message = ['message' => "Done, {$this->model} deleted successfully"];
        $this->view->send($responseCode, $message);
    }

    protected function handleResourceNotFound(): void
    {
        $responseCode = '404';
        $message = ['error' => 'Resource not found'];
        $this->view->send($responseCode, $message);
    }
}
