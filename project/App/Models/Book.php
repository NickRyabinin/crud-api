<?php

namespace App\Models;

use App\Core\Exceptions\InvalidIdException;
use App\Core\Exceptions\InvalidTokenException;
use App\Core\Exceptions\InvalidDataException;

class Book extends Model
{
    protected $entity = 'book';
    protected $properties = ['title', 'author', 'published_at'];
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function store(string $token, array $data): bool
    {
        $hashedToken = base64_decode($token);
        if (!$this->compare($this->properties, $data)) {
            throw new InvalidDataException();
        }
        if (!$this->checkToken($hashedToken)) {
            throw new InvalidTokenException();
        }
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

    public function index(): array
    {
        $query = "SELECT * FROM {$this->entity}s";
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
        $query = "SELECT * FROM {$this->entity}s WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function update(string $id, string $token, array $data): bool
    {
        $hashedToken = base64_decode($token);
        $filteredData = array_intersect_key($data, array_flip($this->properties));
        if (!$this->checkId($id)) {
            throw new InvalidIdException();
        }
        if (!$this->checkToken($hashedToken)) {
            throw new InvalidTokenException();
        }
        if (count($filteredData) === 0) {
            throw new InvalidDataException();
        }
        $query = "UPDATE {$this->entity}s SET";
        foreach ($filteredData as $key => $value) {
            $query = $query . " {$key} = :{$key},";
        }
        $query = substr($query, 0, -1) . " WHERE id = :id";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(":id", $id);
            foreach ($filteredData as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new InvalidDataException();
        }
        return true;
    }

    public function destroy(string $id, string $token): bool
    {
        $hashedToken = base64_decode($token);
        if (!$this->checkId($id)) {
            throw new InvalidIdException();
        }
        if (!$this->checkToken($hashedToken)) {
            throw new InvalidTokenException();
        }
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
