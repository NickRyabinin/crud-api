<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Models\Book;
use App\Core\Exceptions\InvalidDataException;

class BookTest extends TestCase
{
    protected $pdo;

    protected function setUp(): void
    {
        $this->pdo = new \PDO('sqlite::memory:');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec(
            'CREATE TABLE books (
                id INTEGER PRIMARY KEY,
                title TEXT,
                author TEXT,
                published_at DATE)'
        );
    }

    protected function tearDown(): void
    {
        $this->pdo->exec('DROP TABLE books');
        $this->pdo = null;
    }

    public function testIndex()
    {
        $data = [
            ['id' => 1, 'title' => 'Book 1', 'author' => 'Author 1', 'published_at' => '2024-01-01'],
            ['id' => 2, 'title' => 'Book 2', 'author' => 'Author 2', 'published_at' => '2024-02-01']
        ];
        foreach ($data as $book) {
            $this->pdo->exec(
                "INSERT INTO books (title, author, published_at)
                VALUES ('{$book['title']}', '{$book['author']}', '{$book['published_at']}')"
            );
        }
        $book = new Book($this->pdo);
        $result = $book->index();

        $this->assertEquals($data, $result);
    }

    public function testShow()
    {
        $bookData = ['id' => 1, 'title' => 'Book 3', 'author' => 'Author 3', 'published_at' => '2024-03-01'];
        $this->pdo->exec(
            "INSERT INTO books (title, author, published_at)
            VALUES ('{$bookData['title']}', '{$bookData['author']}', '{$bookData['published_at']}')"
        );
        $book = new Book($this->pdo);
        $result = $book->show(1);

        $this->assertEquals($bookData, $result);
    }

    public function testShowBookDoesNotExistWithSuchID()
    {
        $book = new Book($this->pdo);
        $result = $book->show(11);

        $this->assertFalse($result);
    }
}
