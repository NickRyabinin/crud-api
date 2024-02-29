<?php

namespace App\Models;

use App\Core\Exceptions\InvalidDataException;

class Opinion extends Model
{
    public string $entity = 'opinion';
    protected array $properties = ['opinion', 'book_id', 'author_login'];
    protected \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function store(string $token, array $data): bool
    {
        parent::checkToken($token);
        $data['author_login'] = parent::get('user', 'login', 'token', $token);
        parent::compare($this->properties, $data);
        $query = "INSERT INTO {$this->entity}s (opinion, book_id, author_login)
            VALUES (:opinion, :book_id, :author_login)";
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

    public function index(string $parentId): array
    {
        $query = "SELECT * FROM {$this->entity}s WHERE book_id = :book_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':book_id' => $parentId]);
        $result = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }
        return $result;
    }

    public function show(string $parentId, string $childId): array | bool
    {
        $query = "SELECT * FROM {$this->entity}s WHERE book_id = :book_id AND id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':book_id' => $parentId, ':id' => $childId]);
        return $stmt->fetch();
    }
}
