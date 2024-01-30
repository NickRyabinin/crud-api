<?php

namespace App\Tests;

spl_autoload_register(function ($className) {
    $file = __DIR__ . '/../../' . str_replace('\\', '/', $className) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

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
        $this->helper->method('getInputData')->willReturn(['title' => 'Test Book', 'author' => 'Test Author']);
        $this->helper->method('sanitize')->will($this->returnArgument(0));
        $this->helper->method('validate')->will($this->returnArgument(0));

        $this->book->expects($this->once())->method('store')->with('validToken', ['title' => 'Test Book', 'author' => 'Test Author'])->willReturn(true);

        $this->view->expects($this->once())->method('send')->with('201', ['message' => "Done, book added successfully"]);

        $this->controller->create();
    }

    public function testCreateWithInvalidToken()
    {
        $this->helper->method('getId')->willReturn('');
        $this->helper->method('getToken')->willReturn('invalidToken');
        $this->helper->method('getInputData')->willReturn(['title' => 'Test Book', 'author' => 'Test Author']);
        $this->helper->method('sanitize')->will($this->returnArgument(0));
        $this->helper->method('validate')->will($this->returnArgument(0));

        $this->book->expects($this->once())->method('store')->with('invalidToken', ['title' => 'Test Book', 'author' => 'Test Author'])->willThrowException(new InvalidTokenException());

        $this->view->expects($this->once())->method('send')->with('401', ['error' => 'Unauthorized, no such token']);

        $this->controller->create();
    }
}
