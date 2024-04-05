<?php

/**
 * Контроллер UserController - обрабатывает действия пользователя с сущностью 'User'.
 * Вызывает соответствующие методы модели. На основе данных, полученных от модели,
 * формирует результат, передаваемый во View.
 */

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
        if ($id !== '') {
            parent::handleInvalidData();
            return;
        }
        $inputData = $this->helper->getInputData();
        $cleanData = array_map(fn ($param) => $this->helper->sanitize($this->helper->validate($param)), $inputData);
        $login = $cleanData['login'] ?? '';
        $email = $cleanData['email'] ?? '';
        if (!($login && $email)) {
            parent::handleInvalidData();
            return;
        }
        $token = hash('sha256', $email . $login);
        $cleanData['hashed_token'] = $token;
        try {
            $this->user->store($cleanData);
            parent::handleUserCreatedOk($token);
        } catch (InvalidDataException $e) {
            parent::handleInvalidData();
        }
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
