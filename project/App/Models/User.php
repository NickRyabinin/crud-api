<?php

namespace App\Models;

class User extends Model
{
    protected $entity = 'user';
    protected $properties = ['login', 'email', 'hashed_token'];
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function store(array $data): bool
    {
        if ($this->compare($this->properties, $data)) {
            try {
                $query = "INSERT INTO {$this->entity}s (login, email, hashed_token)
                    VALUES (:login, :email, :hashed_token)";
                $stmt = $this->pdo->prepare($query);
                foreach ($data as $key => $value) {
                    $stmt->bindValue(":{$key}", $value);
                }
                $stmt->execute();
                return true;
            } catch (\PDOException $e) {
            }
        }
        return false;
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
        return $stmt->fetch();
    }

    public function destroy(string $token): bool
    {
        $hashedToken = base64_decode($token);
        if ($this->checkToken($hashedToken)) {
            $query = "DELETE FROM {$this->entity}s WHERE hashed_token = :hashed_token";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':hashed_token' => $hashedToken]);
            return true;
        }
        return false;
    }
}
