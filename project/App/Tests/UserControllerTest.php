<?php

/**
 * Класс UserControllerTest - unit-тесты контроллера "UserController".
 */

namespace App\Tests;

use App\Controllers\UserController;
use App\Models\User;
use App\Core\Exceptions\InvalidTokenException;
use App\Core\Exceptions\InvalidDataException;

class UserControllerTest extends BaseControllerTestSetUp
{
    private User $user;
    private UserController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createMock(User::class);
        $this->user->method('__toString')->willReturn('user');
        $this->controller = new UserController($this->user, $this->view, $this->helper);
    }

    public function testCreate(): void
    {
        $data = ['login' => 'Test Login', 'email' => 'Test@Email'];
        extract($data);
        $token = hash('sha256', $email . $login);

        $this->setupTest('', $token, $data);

        $this->user->expects($this->once())->method('store')
            ->with(['login' => $login, 'email' => $email, 'hashed_token' => $token])
            ->willReturn(true);

        $this->view->expects($this->once())->method('send')
            ->with('201', [
                'message' => "Done, user added successfully",
                'token' => base64_encode($token)
            ]);

        $this->controller->create();
    }

    public function testCreateWithInvalidData(): void
    {
        $data = ['login' => 'Test Login', 'email' => 'NotUnique@Email'];
        extract($data);
        $token = hash('sha256', $email . $login);

        $this->setupTest('', $token, $data);

        $this->user->expects($this->once())->method('store')
            ->with(['login' => $login, 'email' => $email, 'hashed_token' => $token])
            ->willThrowException(new InvalidDataException());

        $this->view->expects($this->once())->method('send')
            ->with('400', [
                'error' => 'Invalid input data'
            ]);

        $this->controller->create();
    }

    public function testReadIndex(): void
    {
        $date1 = date('YYYY-MM-DD HH:MM:SS');
        $date2 = date('YYYY-MM-DD HH:MM:SS');
        $data = [
            [
                'id' => 1,
                'login' => 'Test Login 1',
                'created_at' => $date1
            ],
            [
                'id' => 2,
                'login' => 'Test Login 2',
                'created_at' => $date2
            ]
        ];

        $this->setupTest('');

        $this->user->expects($this->once())->method('index')->willReturn($data);
        $this->view->expects($this->once())->method('send')->with('200', $data);
        $this->controller->read();
    }

    public function testReadIndexEmpty(): void
    {
        $this->setupTest('');
        $this->user->expects($this->once())->method('index')->willReturn([]);
        $this->view->expects($this->once())->method('send')
            ->with('404', ['error' => 'No records']);
        $this->controller->read();
    }

    public function testReadShow(): void
    {
        $validId = (string)random_int(1, 10);
        $date = date('YYYY-MM-DD HH:MM:SS');
        $data = [
            [
                'id' => $validId,
                'login' => 'Some Login',
                'created_at' => $date
            ]
        ];

        $this->setupTest($validId);

        $this->user->expects($this->once())->method('show')->willReturn($data);
        $this->view->expects($this->once())->method('send')->with('200', $data);
        $this->controller->read();
    }

    public function testReadShowEmpty(): void
    {
        $this->setupTest((string)random_int(1, 10));
        $this->user->expects($this->once())->method('show')->willReturn(false);
        $this->view->expects($this->once())->method('send')
            ->with('404', ['error' => 'No record with such ID']);
        $this->controller->read();
    }

    public function testUpdate(): void
    {
        $this->view->expects($this->once())->method('send')
            ->with('405', ['error' => 'Method not allowed']);

        $this->controller->update();
    }

    public function testDelete(): void
    {
        $this->setupTest('', 'validToken');

        $this->user->expects($this->once())->method('destroy')
            ->with('validToken')
            ->willReturn(true);

        $this->view->expects($this->once())->method('send')
            ->with('200', ['message' => "Done, user deleted successfully"]);

        $this->controller->delete();
    }

    public function testDeleteWithInvalidData(): void
    {
        $this->setupTest('invalidId', 'validToken');

        $this->view->expects($this->once())->method('send')
            ->with('400', ['error' => 'Invalid input data']);

        $this->controller->delete();
    }

    public function testDeleteWithInvalidToken(): void
    {
        $this->setupTest('', 'invalidToken');

        $this->user->expects($this->once())->method('destroy')
            ->with('invalidToken')
            ->willThrowException(new InvalidTokenException());

        $this->view->expects($this->once())->method('send')
            ->with('401', ['error' => 'Unauthorized, no such token']);

        $this->controller->delete();
    }
}
