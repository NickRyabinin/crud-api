<?php

/**
 * Класс User - модель сущности 'User'.
 * Коммуницирует с БД, выполняя стандартные CRUD-операции.
 */

namespace App\Models;

use App\Core\Exceptions\InvalidDataException;

class User extends Model
{
    public string $entity = 'user';
    protected array $fillableProperties = ['login', 'email', 'hashed_token'];
    protected array $viewableProperties = ['id', 'login', 'created_at'];
    protected \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function store(array $data): bool
    {
        parent::compare($this->fillableProperties, $data);
        $query = "INSERT INTO {$this->entity}s (login, email, hashed_token)
                    VALUES (:login, :email, :hashed_token)";
        try {
            $stmt = $this->pdo->prepare($query);
            foreach ($data as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new InvalidDataException();
        }
        return true;
    }

    public function destroy(string $token): bool
    {
        parent::checkToken($token);
        $hashedToken = base64_decode($token);
        $query = "DELETE FROM {$this->entity}s WHERE hashed_token = :hashed_token";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(":hashed_token", $hashedToken);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new InvalidDataException();
        }
        return true;
    }
}
