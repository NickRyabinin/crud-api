<?php

namespace App\Tests;

use App\Models\User;
use App\Models\Book;
use App\Models\Opinion;
use PHPUnit\Framework\TestCase;

class BaseModelTestSetUp extends TestCase
{
    protected User $user;
    protected Book $book;
    protected Opinion $opinion;
    protected ?\PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = new \PDO('sqlite::memory:');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec(
            'CREATE TABLE users (
                id INTEGER PRIMARY KEY,
                login TEXT,
                email TEXT,
                hashed_token TEXT,
                created_at DATE)'
        );
        $this->pdo->exec(
            'CREATE TABLE books (
                id INTEGER PRIMARY KEY,
                title TEXT,
                author TEXT,
                published_at DATE)'
        );
        $this->user = new User($this->pdo);
        $this->book = new Book($this->pdo);
    }

    protected function tearDown(): void
    {
        $this->pdo->exec('DROP TABLE users');
        $this->pdo->exec('DROP TABLE books');
        $this->pdo = null;
    }

    /* protected function setupTest(
        string $id,
        string $token = '',
        array $inputData = [],
        string | bool $childId = ''
    ): void {
        $this->helper->method('getId')->willReturnMap([
            ['', $id],
            ['nested', $childId],
        ]);
        $this->helper->method('getToken')->willReturn($token);
        $this->helper->method('getInputData')->willReturn($inputData);
        $this->helper->method('sanitize')->willReturnArgument(0);
        $this->helper->method('validate')->willReturnArgument(0);
    } */
}
