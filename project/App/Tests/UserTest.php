<?php

/**
 * Класс UserTest - unit-тесты модели "User".
 */

namespace App\Tests;

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
        $result = $this->user->index('', 1);

        $this->assertEquals($data, $result);
    }

    public function testShow(): void
    {
        $userData = [['id' => 1, 'login' => 'User 3', 'created_at' => '2024-03-01']];
        $this->pdo->exec(
            "INSERT INTO users (login, created_at)
            VALUES ('{$userData[0]['login']}', '{$userData[0]['created_at']}')"
        );
        $result = $this->user->show(1);

        $this->assertEquals($userData, $result);
    }

    public function testShowUserDoesNotExistWithSuchID(): void
    {
        $result = $this->user->show(11);

        $this->assertFalse($result[0]);
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

    public function testDestroy(): void
    {
        $token = parent::makeDefaultUser();
        $result = $this->user->destroy($token);

        $this->assertTrue($result);

        $query = "SELECT * FROM users WHERE hashed_token = '{$token}'";
        $stmt = $this->pdo->query($query);
        $deletedUser = $stmt->fetch();

        $this->assertFalse($deletedUser);
    }
}
