<?php

/**
 * Класс Book - модель сущности 'Book'.
 * Коммуницирует с БД, выполняя стандартные CRUD-операции.
 */

namespace App\Models;

use App\Core\Exceptions\InvalidDataException;

class Book extends Model
{
    public string $entity = 'book';
    protected array $fillableProperties = ['title', 'author', 'published_at'];
    protected array $viewableProperties = ['id', 'title', 'author', 'published_at', 'created_at'];
    protected \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function store(string $token, array $data): bool
    {
        parent::compare($this->fillableProperties, $data);
        parent::checkToken($token);
        $query = "INSERT INTO {$this->entity}s (title, author, published_at)
            VALUES (:title, :author, :published_at)";
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

    public function update(string $id, string $token, array $data): bool
    {
        $filteredData = array_intersect_key($data, array_flip($this->fillableProperties));
        parent::checkId($id);
        parent::checkToken($token);
        if (count($filteredData) === 0) {
            throw new InvalidDataException();
        }
        $query = "UPDATE {$this->entity}s SET";
        foreach ($filteredData as $key => $value) {
            $query = $query . " {$key} = :{$key},";
        }
        $query = rtrim($query, ',') . " WHERE id = :id";
        try {
            $stmt = $this->pdo->prepare($query);
            foreach ($filteredData as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }
            $stmt->bindValue(":id", $id);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new InvalidDataException();
        }
        return true;
    }

    public function destroy(string $id, string $token): bool
    {
        parent::checkId($id);
        parent::checkToken($token);
        $query = "DELETE FROM {$this->entity}s WHERE id= :id";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new InvalidDataException();
        }
        return true;
    }
}
