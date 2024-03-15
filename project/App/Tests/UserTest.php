<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Models\User;
use App\Core\Exceptions\InvalidDataException;

class UserTest extends TestCase
{
    protected $pdo;

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
    }

    protected function tearDown(): void
    {
        $this->pdo->exec('DROP TABLE users');
        $this->pdo = null;
    }

    public function testIndex()
    {
        $data = [
            ['id' => 1, 'login' => 'User 1', 'created_at' => '2024-01-01'],
            ['id' => 2, 'login' => 'User 2', 'created_at' => '2024-02-01']
        ];
        /* $token1 = hash('sha256', $data[0]['email'] . $data[0]['login']);
        $data[0]['hashed_token'] = $token1;
        $token2 = hash('sha256', $data[1]['email'] . $data[1]['login']);
        $data[1]['hashed_token'] = $token2; */
        foreach ($data as $user) {
            $this->pdo->exec(
                "INSERT INTO users (login, created_at)
                VALUES ('{$user['login']}', '{$user['created_at']}')"
            );
        }
        $user = new User($this->pdo);
        $result = $user->index();

        $this->assertEquals($data, $result);
    }

    public function testShow()
    {
        $userData = ['id' => 1, 'login' => 'User 3', 'created_at' => '2024-03-01'];
        $this->pdo->exec(
            "INSERT INTO users (login, created_at)
            VALUES ('{$userData['login']}', '{$userData['created_at']}')"
        );
        $user = new User($this->pdo);
        $result = $user->show(1);

        $this->assertEquals($userData, $result);
    }

    public function testShowUserDoesNotExistWithSuchID()
    {
        $user = new User($this->pdo);
        $result = $user->show(11);

        $this->assertFalse($result);
    }
}
