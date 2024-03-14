<?php

namespace App\Models;

use App\Core\Exceptions\InvalidDataException;

class User extends Model
{
    public string $entity = 'user';
    protected array $properties = ['login', 'email', 'hashed_token'];
    protected \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function store(array $data): bool
    {
        parent::compare($this->properties, $data);
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

    public function index(): array
    {
        $query = "SELECT id, login, created_at FROM {$this->entity}s";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }
        return $result;
    }

    public function show(string $id): array | bool
    {
        $query = "SELECT id, login, created_at FROM {$this->entity}s WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function destroy(string $token): bool
    {
        parent::checkToken($token);
        $query = "DELETE FROM {$this->entity}s WHERE hashed_token = :hashed_token";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(":hashed_token", $$hashedToken);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new InvalidDataException();
        }
        return true;
    }
}
