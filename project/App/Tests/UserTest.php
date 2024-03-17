<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Models\User;
use App\Core\Exceptions\InvalidTokenException;

class UserTest extends BaseModelTestSetUp
{
    public function testIndex(): void
    {
        $data = [
            ['id' => 1, 'login' => 'User 1', 'created_at' => '2024-01-01'],
            ['id' => 2, 'login' => 'User 2', 'created_at' => '2024-02-01']
        ];
        foreach ($data as $user) {
            $this->pdo->exec(
                "INSERT INTO users (login, created_at)
                VALUES ('{$user['login']}', '{$user['created_at']}')"
            );
        }
        $result = $this->user->index();

        $this->assertEquals($data, $result);
    }

    public function testShow(): void
    {
        $userData = ['id' => 1, 'login' => 'User 3', 'created_at' => '2024-03-01'];
        $this->pdo->exec(
            "INSERT INTO users (login, created_at)
            VALUES ('{$userData['login']}', '{$userData['created_at']}')"
        );
        $result = $this->user->show(1);

        $this->assertEquals($userData, $result);
    }

    public function testShowUserDoesNotExistWithSuchID(): void
    {
        $result = $this->user->show(11);

        $this->assertFalse($result);
    }

    public function testStore(): void
    {
        $userData = ['login' => 'User 1', 'email' => 'email1@email.net'];
        $token = hash('sha256', $userData['email'] . $userData['login']);
        $userData['hashed_token'] = $token;
        $result = $this->user->store($userData);

        $this->assertTrue($result);

        $stmt = $this->pdo->query('SELECT * FROM users');
        $insertedUser = $stmt->fetch();

        $this->assertEquals($userData['login'], $insertedUser['login']);
        $this->assertEquals($userData['email'], $insertedUser['email']);
        $this->assertEquals($userData['hashed_token'], hash('sha256', $insertedUser['email'] . $insertedUser['login']));
    }

    public function testDestroyWithInvalidToken(): void
    {
        $this->expectException(InvalidTokenException::class);

        $this->user->destroy('invalid_token');
    }
}
