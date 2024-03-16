<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Models\Book;
use App\Core\Exceptions\InvalidDataException;

class BookTest extends BaseModelTestSetUp
{
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
        $result = $this->book->index();

        $this->assertEquals($data, $result);
    }

    public function testShow()
    {
        $bookData = ['id' => 1, 'title' => 'Book 3', 'author' => 'Author 3', 'published_at' => '2024-03-01'];
        $this->pdo->exec(
            "INSERT INTO books (title, author, published_at)
            VALUES ('{$bookData['title']}', '{$bookData['author']}', '{$bookData['published_at']}')"
        );
        $result = $this->book->show(1);

        $this->assertEquals($bookData, $result);
    }

    public function testShowBookDoesNotExistWithSuchID()
    {
        $result = $this->book->show(11);

        $this->assertFalse($result);
    }

    /* public function testStore(): void
    {
        $bookData = ['title' => 'New Book', 'author' => 'Author 1', 'published_at' => '2024-01-01'];
        $result = $this->book->store('token', $bookData);

        $this->assertTrue($result);

        $stmt = $this->pdo->query('SELECT * FROM books');
        $insertedBook = $stmt->fetch();

        $this->assertEquals($bookData['title'], $insertedBook['title']);
        $this->assertEquals($bookData['author'], $insertedBook['author']);
        $this->assertEquals($bookData['published_at'], $insertedBook['published_at']);
    } */
}
