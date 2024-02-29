<?php

namespace App\Models;

use App\Core\Exceptions\InvalidIdException;
use App\Core\Exceptions\InvalidTokenException;
use App\Core\Exceptions\InvalidDataException;

abstract class Model
{
    protected \PDO $pdo;
    public string $entity;

    public function __toString(): string
    {
        return $this->entity;
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

    protected function checkId(string $id): void
    {
        $query = "SELECT EXISTS (SELECT id FROM {$this->entity}s WHERE id = :id) AS isExists";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':id' => $id]);
        if (($stmt->fetch())['isExists'] === 0) {
            throw new InvalidIdException();
        }
    }

    protected function checkToken(string $token): void
    {
        $hashedToken = base64_decode($token);
        $query = "SELECT EXISTS (SELECT id FROM users WHERE hashed_token = :hashed_token) AS isExists";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':hashed_token' => $hashedToken]);
        if (($stmt->fetch())['isExists'] === 0) {
            throw new InvalidTokenException();
        }
    }

    protected function compare(array $properties, array $input): void
    {
        if (!(count($properties) === count($input) && array_diff($properties, array_keys($input)) === [])) {
            throw new InvalidDataException();
        };
    }

    protected function get(string $model, string $field, string $conditionKey, string $conditionValue)
    {
        if ($conditionKey === 'token') {
            $conditionKey = 'hashed_token';
            $conditionValue = base64_decode($conditionValue);
        }
        $query = "SELECT {$field} AS 'result' FROM {$model}s WHERE {$conditionKey} = :{$conditionKey}";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([":{$conditionKey}" => $conditionValue]);
        } catch (\PDOException $e) {
            throw new InvalidDataException();
        }
        return $stmt->fetch(\PDO::FETCH_ASSOC)['result'];
    }
}
