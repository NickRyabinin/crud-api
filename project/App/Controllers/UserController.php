<?php

namespace App\Controllers;

use App\Models\User;
use App\Views\View;
use App\Core\Helper;
use App\Core\Exceptions\InvalidTokenException;
use App\Core\Exceptions\InvalidDataException;

class UserController extends Controller
{
    protected $user;
    protected $view;
    protected $helper;

    public function __construct(User $user, View $view, Helper $helper)
    {
        parent::__construct($user);
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

    public function update()
    {
        return parent::handleInvalidMethod();
    }

    public function delete(): void
    {
        $id = $this->helper->getId();
        $token = $this->helper->getToken();
        if ($id !== '') {
            parent::handleInvalidData();
            return;
        }
        try {
            $this->user->destroy($token);
            parent::handleDeletedOk();
        } catch (InvalidTokenException $e) {
            parent::handleInvalidToken();
        } catch (InvalidDataException $e) {
            parent::handleInvalidData();
        }
    }
}
