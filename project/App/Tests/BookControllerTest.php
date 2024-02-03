<?php

namespace App\Tests;

use App\Controllers\BookController;
use App\Models\Book;
use App\Views\View;
use App\Core\Helper;
use App\Core\Exceptions\InvalidIdException;
use App\Core\Exceptions\InvalidTokenException;
use App\Core\Exceptions\InvalidDataException;
use PHPUnit\Framework\TestCase;

class BookControllerTest extends TestCase
{
    private Helper $helper;
    private View $view;
    private Book $book;
    private BookController $controller;

    protected function setUp(): void
    {
        $this->book = $this->createMock(Book::class);
        $this->view = $this->createMock(View::class);
        $this->helper = $this->createMock(Helper::class);
        $this->controller = new BookController($this->book, $this->view, $this->helper);
    }

    public function testCreate()
    {
        $this->helper->method('getId')->willReturn('');
        $this->helper->method('getToken')->willReturn('validToken');
        $this->helper->method('getInputData')->willReturn([
            'title' => 'Test Book', 'author' => 'Test Author', 'published_at' => date('Y')
        ]);
        $this->helper->method('sanitize')->willReturnArgument(0);
        $this->helper->method('validate')->willReturnArgument(0);

        $this->book->expects($this->once())->method('store')
            ->with('validToken', ['title' => 'Test Book', 'author' => 'Test Author', 'published_at' => date('Y')])
            ->willReturn(true);

        $this->view->expects($this->once())->method('send')
            ->with('201', ['message' => "Done, book added successfully"]);

        $this->controller->create();
    }

    public function testCreateWithInvalidToken()
    {
        $this->helper->method('getId')->willReturn('');
        $this->helper->method('getToken')->willReturn('invalidToken');
        $this->helper->method('getInputData')->willReturn([
            'title' => 'Test Book', 'author' => 'Test Author', 'published_at' => date('Y')
        ]);
        $this->helper->method('sanitize')->willReturnArgument(0);
        $this->helper->method('validate')->willReturnArgument(0);

        $this->book->expects($this->once())->method('store')
            ->with('invalidToken', ['title' => 'Test Book', 'author' => 'Test Author', 'published_at' => date('Y')])
            ->willThrowException(new InvalidTokenException());

        $this->view->expects($this->once())->method('send')
            ->with('401', ['error' => 'Unauthorized, no such token']);

        $this->controller->create();
    }

    public function testCreateWithInvalidData()
    {
        $this->helper->method('getId')->willReturn('');
        $this->helper->method('getToken')->willReturn('validToken');
        $this->helper->method('getInputData')->willReturn([
            'label' => 'Test Book', 'author' => 'Test Author', 'published_at' => date('Y')
        ]);
        $this->helper->method('sanitize')->willReturnArgument(0);
        $this->helper->method('validate')->willReturnArgument(0);

        $this->book->expects($this->once())->method('store')
            ->with('validToken', ['label' => 'Test Book', 'author' => 'Test Author', 'published_at' => date('Y')])
            ->willThrowException(new InvalidDataException());

        $this->view->expects($this->once())->method('send')
            ->with('400', ['error' => 'Invalid input data']);

        $this->controller->create();
    }

    public function testReadIndex()
    {
        $this->helper->method('getId')->willReturn('');
        $year1 = rand(1000, 2024);
        $year2 = rand(1000, 2024);
        $date1 = date('YYYY-MM-DD HH:MM:SS');
        $date2 = date('YYYY-MM-DD HH:MM:SS');
        $data = [
            [
                'id' => 1,
                'title' => 'Title 1',
                'author' => 'Author 1',
                'published_at' => $year1,
                'created_at' => $date1
            ],
            [
                'id' => 2,
                'title' => 'Title 2',
                'author' => 'Author 2',
                'published_at' => $year2,
                'created_at' => $date2
            ]
        ];
        $this->book->expects($this->once())->method('index')->willReturn($data);
        $this->view->expects($this->once())->method('send')->with('200', $data);
        $this->controller->read();
    }

    public function testReadShow()
    {
        $validId = (string)random_int(1, 10);
        $this->helper->method('getId')->willReturn($validId);
        $year = rand(1000, 2024);
        $date = date('YYYY-MM-DD HH:MM:SS');
        $data = [
            [
                'id' => $validId,
                'title' => 'Some Title',
                'author' => 'Some Author',
                'published_at' => $year,
                'created_at' => $date
            ]
        ];
        $this->book->expects($this->once())->method('show')->willReturn($data);
        $this->view->expects($this->once())->method('send')->with('200', $data);
        $this->controller->read($validId);
    }

    public function testDelete()
    {
        $validId = (string)random_int(1, 10);
        $this->helper->method('getId')->willReturn($validId);
        $this->helper->method('getToken')->willReturn('validToken');

        $this->book->expects($this->once())->method('destroy')
            ->with($validId, 'validToken')
            ->willReturn(true);

        $this->view->expects($this->once())->method('send')
            ->with('200', ['message' => "Done, book deleted successfully"]);

        $this->controller->delete();
    }

    public function testDeleteWithInvalidToken()
    {
        $validId = (string)random_int(1, 10);
        $this->helper->method('getId')->willReturn($validId);
        $this->helper->method('getToken')->willReturn('invalidToken');

        $this->book->expects($this->once())->method('destroy')
            ->with($validId, 'invalidToken')
            ->willReturn('');

        $this->view->expects($this->once())->method('send')
            ->with('401', ['error' => 'Unauthorized, no such token']);

        $this->controller->delete();
    }
}
