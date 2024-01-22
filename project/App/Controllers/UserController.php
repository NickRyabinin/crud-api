<?php

namespace App\Controllers;

use App\Models\User;
use App\Views\View;
use App\Core\Helper;

class UserController
{
    private $user;
    private $view;
    private $helper;

    public function __construct(User $user, View $view, Helper $helper)
    {
        $this->user = $user;
        $this->view = $view;
        $this->helper = $helper;
    }

    public function create()
    {
        $id = $this->helper->getId();
        $responseCode = '400';
        $message = ['error' => 'Invalid input data'];
        if ($id === '') {
            $inputData = $this->helper->getInputData();
            $cleanData = array_map(fn ($param) => $this->helper->sanitize($this->helper->validate($param)), $inputData);
            $login = $cleanData['login'] ?? '';
            $email = $cleanData['email'] ?? '';
            if ($login && $email) {
                $token = hash('sha256', $login . $email . time());
                $hashedToken = password_hash($token, PASSWORD_DEFAULT);
                var_dump($hashedToken);
                $cleanData['hashed_token'] = $hashedToken;
                if ($this->user->store($cleanData)) {
                    $responseCode = '201';
                    $message = [
                        'message' => "Done, user added successfully",
                        'token' => "{$token}"
                    ];
                }
            }
        }
        $this->view->send($responseCode, $message);
    }

    public function read()
    {
        $id = $this->helper->getId();
        if ($id === '') {
            $message = $this->user->index();
            if ($message === []) {
                $responseCode = '404';
                $message = ['error' => 'No records'];
            } else {
                $responseCode = '200';
            }
        } elseif ($id === false) {
            $responseCode = '400';
            $message = ['error' => 'Invalid ID'];
        } else {
            $message = $this->user->show($id);
            if ($message === false) {
                $responseCode = '404';
                $message = ['error' => 'No record with such ID'];
            } else {
                $responseCode = '200';
            }
        }
        $this->view->send($responseCode, $message);
    }

    public function update()
    {
        $id = $this->helper->getId();
        if ($id === '' || $id === false) {
            $responseCode = '400';
            $message = ['error' => 'Invalid ID'];
        } else {
            $inputData = $this->helper->getInputData();
            $cleanData = array_map(fn ($param) => $this->helper->sanitize($this->helper->validate($param)), $inputData);
            $message = $this->user->update($id, $cleanData);
            if ($message === false) {
                $responseCode = '404';
                $message = ['error' => 'No record with such ID'];
            } elseif ($message === null) {
                $responseCode = '400';
                $message = ['error' => 'Invalid input data'];
            } else {
                $responseCode = '200';
                $message = ["Done, user updated successfully"];
            }
        }
        $this->view->send($responseCode, $message);
    }

    public function delete()
    {
        $id = $this->helper->getId();
        if ($id === '' || $id === false) {
            $responseCode = '400';
            $message = ['error' => 'Invalid ID'];
        } else {
            $message = $this->user->destroy($id);
            if ($message === false) {
                $responseCode = '404';
                $message = ['error' => 'No record with such ID'];
            } else {
                $responseCode = '200';
                $message = ["Done, user deleted successfully"];
            }
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
