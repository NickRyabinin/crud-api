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
    private string $validId;

    protected function setUp(): void
    {
        $this->book = $this->createMock(Book::class);
        $this->view = $this->createMock(View::class);
        $this->helper = $this->createMock(Helper::class);
        $this->controller = new BookController($this->book, $this->view, $this->helper);
        $this->validId = (string)random_int(1, 10);
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
        $this->helper->method('getId')->willReturn($this->validId);
        $year = rand(1000, 2024);
        $date = date('YYYY-MM-DD HH:MM:SS');
        $data = [
            [
                'id' => $this->validId,
                'title' => 'Some Title',
                'author' => 'Some Author',
                'published_at' => $year,
                'created_at' => $date
            ]
        ];
        $this->book->expects($this->once())->method('show')->willReturn($data);
        $this->view->expects($this->once())->method('send')->with('200', $data);
        $this->controller->read();
    }

    public function testUpdate()
    {
        $data = [
            'title' => 'Updated Title',
            'author' => 'Updated Author',
            'published_at' => date('Y')
        ];
        $this->helper->method('getId')->willReturn($this->validId);
        $this->helper->method('getToken')->willReturn('validToken');
        $this->helper->method('getInputData')->willReturn($data);
        $this->helper->method('sanitize')->willReturnArgument(0);
        $this->helper->method('validate')->willReturnArgument(0);

        $this->book->expects($this->once())->method('update')
            ->with($this->validId, 'validToken', $data)
            ->willReturn(true);

        $this->view->expects($this->once())->method('send')
            ->with('200', ['message' => "Done, book updated successfully"]);

        $this->controller->update();
    }

    public function testUpdateWithUnexistedId()
    {
        $data = [
            'title' => 'Updated Title',
            'author' => 'Updated Author',
            'published_at' => date('Y')
        ];
        $this->helper->method('getId')->willReturn('unexistedId');
        $this->helper->method('getToken')->willReturn('validToken');
        $this->helper->method('getInputData')->willReturn($data);
        $this->helper->method('sanitize')->willReturnArgument(0);
        $this->helper->method('validate')->willReturnArgument(0);

        $this->book->expects($this->once())->method('update')
            ->with('unexistedId', 'validToken', $data)
            ->willThrowException(new InvalidIdException());

        $this->view->expects($this->once())->method('send')
            ->with('404', ['error' => 'No record with such ID']);

        $this->controller->update();
    }

    public function testUpdateWithInvalidToken()
    {
        $data = [
            'title' => 'Updated Title',
            'author' => 'Updated Author',
            'published_at' => date('Y')
        ];
        $this->helper->method('getId')->willReturn($this->validId);
        $this->helper->method('getToken')->willReturn('invalidToken');
        $this->helper->method('getInputData')->willReturn($data);
        $this->helper->method('sanitize')->willReturnArgument(0);
        $this->helper->method('validate')->willReturnArgument(0);

        $this->book->expects($this->once())->method('update')
            ->with($this->validId, 'invalidToken', $data)
            ->willThrowException(new InvalidTokenException());

        $this->view->expects($this->once())->method('send')
            ->with('401', ['error' => 'Unauthorized, no such token']);

        $this->controller->update();
    }

    public function testUpdateWithInvalidData()
    {
        $invalidData = [
            'label' => 'Updated Title',
            'author' => 'Updated Author',
            'published_at' => 'year'
        ];
        $this->helper->method('getId')->willReturn($this->validId);
        $this->helper->method('getToken')->willReturn('validToken');
        $this->helper->method('getInputData')->willReturn($invalidData);
        $this->helper->method('sanitize')->willReturnArgument(0);
        $this->helper->method('validate')->willReturnArgument(0);

        $this->book->expects($this->once())->method('update')
            ->with($this->validId, 'validToken', $invalidData)
            ->willThrowException(new InvalidDataException());

        $this->view->expects($this->once())->method('send')
            ->with('400', ['error' => 'Invalid input data']);

        $this->controller->update();
    }

    public function testDelete()
    {
        $this->helper->method('getId')->willReturn($this->validId);
        $this->helper->method('getToken')->willReturn('validToken');

        $this->book->expects($this->once())->method('destroy')
            ->with($this->validId, 'validToken')
            ->willReturn(true);

        $this->view->expects($this->once())->method('send')
            ->with('200', ['message' => "Done, book deleted successfully"]);

        $this->controller->delete();
    }

    public function testDeleteWithUnexistedId()
    {
        $this->helper->method('getId')->willReturn('unexistedId');
        $this->helper->method('getToken')->willReturn('validToken');

        $this->book->expects($this->once())->method('destroy')
            ->with('unexistedId', 'validToken')
            ->willReturn(false);

        $this->view->expects($this->once())->method('send')
            ->with('404', ['error' => 'No record with such ID']);

        $this->controller->delete();
    }

    public function testDeleteWithInvalidToken()
    {
        $this->helper->method('getId')->willReturn($this->validId);
        $this->helper->method('getToken')->willReturn('invalidToken');

        $this->book->expects($this->once())->method('destroy')
            ->with($this->validId, 'invalidToken')
            ->willReturn('');

        $this->view->expects($this->once())->method('send')
            ->with('401', ['error' => 'Unauthorized, no such token']);

        $this->controller->delete();
    }
}
