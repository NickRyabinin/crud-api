<?php

/**
 * Класс BookTest - unit-тесты модели "Book".
 */

namespace App\Tests;

use App\Core\Exceptions\InvalidDataException;
use App\Core\Exceptions\InvalidTokenException;

class BookTest extends BaseModelTestSetUp
{
    public function testIndex(): void
    {
        $data = [
            [
                'id' => 1, 'title' => 'Book 1', 'author' => 'Author 1',
                'published_at' => '1991', 'created_at' => '2024-01-01'
            ],
            [
                'id' => 2, 'title' => 'Book 2', 'author' => 'Author 2',
                'published_at' => '2020', 'created_at' => '2024-02-01'
            ]
        ];
        foreach ($data as $book) {
            $this->pdo->exec(
                "INSERT INTO books (title, author, published_at, created_at)
                VALUES ('{$book['title']}', '{$book['author']}', '{$book['published_at']}', '{$book['created_at']}')"
            );
        }
        $result = $this->book->index('', 1);

        $this->assertEquals($data, $result);
    }

    public function testShow(): void
    {
        $bookData = [[
            'id' => 1, 'title' => 'Book 3', 'author' => 'Author 3',
            'published_at' => '1991', 'created_at' => '2024-01-01'
        ]];
        $this->pdo->exec(
            "INSERT INTO books (title, author, published_at, created_at)
            VALUES (
                '{$bookData[0]['title']}', '{$bookData[0]['author']}',
                '{$bookData[0]['published_at']}', '{$bookData[0]['created_at']}'
            )"
        );
        $result = $this->book->show(1);

        $this->assertEquals($bookData, $result);
    }

    public function testShowBookDoesNotExistWithSuchID(): void
    {
        $result = $this->book->show(11);

        $this->assertFalse($result[0]);
    }

    public function testStore(): void
    {
        $token = parent::makeDefaultUser();
        $bookData = ['title' => 'New Book', 'author' => 'Author 1', 'published_at' => '2024'];
        $result = $this->book->store($token, $bookData);

        $this->assertTrue($result);

        $stmt = $this->pdo->query('SELECT * FROM books');
        $insertedBook = $stmt->fetch();

        $this->assertEquals($bookData['title'], $insertedBook['title']);
        $this->assertEquals($bookData['author'], $insertedBook['author']);
        $this->assertEquals($bookData['published_at'], $insertedBook['published_at']);
    }

    public function testStoreWithInvalidData(): void
    {
        $incompleteData = ['title' => 'New Book'];
        $token = parent::makeDefaultUser();

        $this->expectException(InvalidDataException::class);

        $this->book->store($token, $incompleteData);
    }

    public function testStoreWithInvalidToken(): void
    {
        $bookData = ['title' => 'New Book', 'author' => 'Author 1', 'published_at' => '2024'];

        $this->expectException(InvalidTokenException::class);

        $this->book->store('invalid_token', $bookData);
    }

    public function testUpdate(): void
    {
        $token = parent::makeDefaultBook();
        $id = 1;
        $bookData = ['title' => 'Updated Book', 'author' => 'Updated Author', 'published_at' => '2024'];
        $result = $this->book->update($id, $token, $bookData);

        $this->assertTrue($result);

        $stmt = $this->pdo->query('SELECT * FROM books');
        $updatedBook = $stmt->fetch();

        $this->assertEquals($bookData['title'], $updatedBook['title']);
        $this->assertEquals($bookData['author'], $updatedBook['author']);
        $this->assertEquals($bookData['published_at'], $updatedBook['published_at']);
    }

    public function testDestroy(): void
    {
        $token = parent::makeDefaultBook();
        $id = 1;
        $result = $this->book->destroy($id, $token);

        $this->assertTrue($result);
    }
}
