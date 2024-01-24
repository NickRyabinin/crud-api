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
                $token = hash('sha256', $email . $login);
                $cleanData['hashed_token'] = $token;
                if ($this->user->store($cleanData)) {
                    $responseCode = '201';
                    $message = [
                        'message' => "Done, user added successfully",
                        'token' => base64_encode($token)
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
        return $this->invalidMethod();
    }

    public function delete()
    {
        $id = $this->helper->getId();
        $token = $this->helper->getToken();
        $responseCode = '400';
        $message = ['error' => 'Invalid input data'];
        if ($id === '') {
            $message = $this->user->destroy($token);
            if ($message === false) {
                $responseCode = '401';
                $message = ['error' => 'No record with such token'];
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
