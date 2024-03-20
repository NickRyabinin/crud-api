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

    protected function makeDefaultUser(): string
    {
        $userData = ['login' => 'Default User', 'email' => 'default_email@email.net'];
        $token = hash('sha256', $userData['email'] . $userData['login']);
        $userData['hashed_token'] = $token;
        $this->user->store($userData);
        return base64_encode($token);
    }

    protected function makeDefaultBook(): string
    {
        $bookData = ['title' => 'New Book', 'author' => 'New Author', 'published_at' => '2024-01-01'];
        $token = $this->makeDefaultUser();
        $this->book->store($token, $bookData);
        return $token;
    }
}
