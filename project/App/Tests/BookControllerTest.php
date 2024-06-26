<?php

/**
 * Класс BookControllerTest - unit-тесты контроллера "BookController".
 */

namespace App\Tests;

use App\Controllers\BookController;
use App\Models\Book;
use App\Core\Exceptions\InvalidIdException;
use App\Core\Exceptions\InvalidTokenException;
use App\Core\Exceptions\InvalidDataException;

class BookControllerTest extends BaseControllerTestSetUp
{
    private Book $book;
    private BookController $controller;
    private string $validId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->book = $this->createMock(Book::class);
        $this->book->method('__toString')->willReturn('book');
        $this->controller = new BookController($this->book, $this->view, $this->helper);
        $this->validId = (string)random_int(1, 10);
    }

    public function testCreate(): void
    {
        $data = [
            'title' => 'Test Book', 'author' => 'Test Author', 'published_at' => date('Y')
        ];

        $this->setupTest('', 'validToken', $data);

        $this->book->expects($this->once())->method('store')
            ->with('validToken', $data)
            ->willReturn(true);

        $this->view->expects($this->once())->method('send')
            ->with('201', ['message' => "Done, book added successfully"]);

        $this->controller->create();
    }

    public function testCreateWithInvalidToken(): void
    {
        $data = [
            'title' => 'Test Book', 'author' => 'Test Author', 'published_at' => date('Y')
        ];

        $this->setupTest('', 'invalidToken', $data);

        $this->book->expects($this->once())->method('store')
            ->with('invalidToken', $data)
            ->willThrowException(new InvalidTokenException());

        $this->view->expects($this->once())->method('send')
            ->with('401', ['error' => 'Unauthorized, no such token']);

        $this->controller->create();
    }

    public function testCreateWithInvalidData(): void
    {
        $this->setupTest('', 'validToken', []);

        $this->book->expects($this->once())->method('store')
            ->with('validToken', [])
            ->willThrowException(new InvalidDataException());

        $this->view->expects($this->once())->method('send')
            ->with('400', ['error' => 'Invalid input data']);

        $this->controller->create();
    }

    public function testReadIndex(): void
    {
        $year1 = rand(1000, 2024);
        $year2 = rand(1000, 2024);
        $date1 = date('YYYY-MM-DD HH:MM:SS');
        $date2 = date('YYYY-MM-DD HH:MM:SS');
        $data = ['items' =>
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
        $this->setupTest('');
        $this->book->expects($this->once())->method('index')->willReturn($data);
        $this->view->expects($this->once())->method('send')->with('200', $data);
        $this->controller->read();
    }

    public function testReadIndexEmpty(): void
    {
        $this->setupTest('');
        $this->book->expects($this->once())->method('index')->willReturn(['items' => []]);
        $this->view->expects($this->once())->method('send')
            ->with('404', ['error' => 'No records']);
        $this->controller->read();
    }

    public function testReadShow(): void
    {
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
        $this->setupTest($this->validId);
        $this->book->expects($this->once())->method('show')->willReturn($data);
        $this->view->expects($this->once())->method('send')->with('200', $data);
        $this->controller->read();
    }

    public function testReadShowEmpty(): void
    {
        $this->setupTest($this->validId);
        $this->book->expects($this->once())->method('show')->willReturn([false]);
        $this->view->expects($this->once())->method('send')
            ->with('404', ['error' => 'No record with such ID']);
        $this->controller->read();
    }

    public function testUpdate(): void
    {
        $data = [
            'title' => 'Updated Title',
            'author' => 'Updated Author',
            'published_at' => date('Y')
        ];

        $this->setupTest($this->validId, 'validToken', $data);

        $this->book->expects($this->once())->method('update')
            ->with($this->validId, 'validToken', $data)
            ->willReturn(true);

        $this->view->expects($this->once())->method('send')
            ->with('200', ['message' => "Done, book updated successfully"]);

        $this->controller->update();
    }

    public function testUpdateWithUnexistedId(): void
    {
        $data = [
            'title' => 'Updated Title',
            'author' => 'Updated Author',
            'published_at' => date('Y')
        ];

        $this->setupTest('unexistedId', 'validToken', $data);

        $this->book->expects($this->once())->method('update')
            ->with('unexistedId', 'validToken', $data)
            ->willThrowException(new InvalidIdException());

        $this->view->expects($this->once())->method('send')
            ->with('404', ['error' => 'No record with such ID']);

        $this->controller->update();
    }

    public function testUpdateWithInvalidToken(): void
    {
        $data = [
            'title' => 'Updated Title',
            'author' => 'Updated Author',
            'published_at' => date('Y')
        ];

        $this->setupTest($this->validId, 'invalidToken', $data);

        $this->book->expects($this->once())->method('update')
            ->with($this->validId, 'invalidToken', $data)
            ->willThrowException(new InvalidTokenException());

        $this->view->expects($this->once())->method('send')
            ->with('401', ['error' => 'Unauthorized, no such token']);

        $this->controller->update();
    }

    public function testUpdateWithInvalidData(): void
    {
        $invalidData = [
            'label' => 'Updated Title',
            'author' => 'Updated Author',
            'published_at' => 'year'
        ];

        $this->setupTest($this->validId, 'validToken', $invalidData);

        $this->book->expects($this->once())->method('update')
            ->with($this->validId, 'validToken', $invalidData)
            ->willThrowException(new InvalidDataException());

        $this->view->expects($this->once())->method('send')
            ->with('400', ['error' => 'Invalid input data']);

        $this->controller->update();
    }

    public function testDelete(): void
    {
        $this->setupTest($this->validId, 'validToken');

        $this->book->expects($this->once())->method('destroy')
            ->with($this->validId, 'validToken')
            ->willReturn(true);

        $this->view->expects($this->once())->method('send')
            ->with('200', ['message' => "Done, book deleted successfully"]);

        $this->controller->delete();
    }

    public function testDeleteWithUnexistedId(): void
    {
        $this->setupTest('unexistedId', 'validToken');

        $this->book->expects($this->once())->method('destroy')
            ->with('unexistedId', 'validToken')
            ->willThrowException(new InvalidIdException());

        $this->view->expects($this->once())->method('send')
            ->with('404', ['error' => 'No record with such ID']);

        $this->controller->delete();
    }

    public function testDeleteWithInvalidToken(): void
    {
        $this->setupTest($this->validId, 'invalidToken');

        $this->book->expects($this->once())->method('destroy')
            ->with($this->validId, 'invalidToken')
            ->willThrowException(new InvalidTokenException());

        $this->view->expects($this->once())->method('send')
            ->with('401', ['error' => 'Unauthorized, no such token']);

        $this->controller->delete();
    }
}
