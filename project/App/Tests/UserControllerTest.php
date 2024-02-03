<?php

namespace App\Tests;

use App\Controllers\UserController;
use App\Models\User;
use App\Views\View;
use App\Core\Helper;
use PHPUnit\Framework\TestCase;

class UserControllerTest extends TestCase
{
    private Helper $helper;
    private View $view;
    private User $user;
    private UserController $controller;

    protected function setUp(): void
    {
        $this->user = $this->createMock(User::class);
        $this->view = $this->createMock(View::class);
        $this->helper = $this->createMock(Helper::class);
        $this->controller = new UserController($this->user, $this->view, $this->helper);
    }

    public function testCreate()
    {
        $this->helper->method('getId')->willReturn('');
        $this->helper->method('getInputData')->willReturn([
            'login' => 'Test Login', 'email' => 'Test@Email'
        ]);
        $this->helper->method('sanitize')->willReturnArgument(0);
        $this->helper->method('validate')->willReturnArgument(0);

        $login = 'Test Login';
        $email = 'Test@Email';
        $token = hash('sha256', $email . $login);
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

    public function testReadIndex()
    {
        $this->helper->method('getId')->willReturn('');
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
        $this->user->expects($this->once())->method('index')->willReturn($data);
        $this->view->expects($this->once())->method('send')->with('200', $data);
        $this->controller->read();
    }

    public function testReadShow()
    {
        $validId = (string)random_int(1, 10);
        $this->helper->method('getId')->willReturn($validId);
        $date = date('YYYY-MM-DD HH:MM:SS');
        $data = [
            [
                'id' => $validId,
                'login' => 'Some Login',
                'created_at' => $date
            ]
        ];
        $this->user->expects($this->once())->method('show')->willReturn($data);
        $this->view->expects($this->once())->method('send')->with('200', $data);
        $this->controller->read($validId);
    }

    public function testDelete()
    {
        $this->helper->method('getId')->willReturn('');
        $this->helper->method('getToken')->willReturn('validToken');

        $this->user->expects($this->once())->method('destroy')
            ->with('validToken')
            ->willReturn(true);

        $this->view->expects($this->once())->method('send')
            ->with('200', ['message' => "Done, user deleted successfully"]);

        $this->controller->delete();
    }

    public function testDeleteWithInvalidToken()
    {
        $this->helper->method('getId')->willReturn('');
        $this->helper->method('getToken')->willReturn('invalidToken');

        $this->user->expects($this->once())->method('destroy')
            ->with('invalidToken')
            ->willReturn(false);

        $this->view->expects($this->once())->method('send')
            ->with('401', ['error' => 'Unauthorized, no such token']);

        $this->controller->delete();
    }
}
