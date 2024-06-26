<?php

/**
 * Абстрактный класс Model - родительский класс для моделей сущностей.
 * Модели коммуницируют с Базой данных.
 *
 * @todo выделить в наследники минимум два абстрактных класса, от которых будут
 * наследоваться соответственно модели связанных (nested) и одиночных (single)
 * сущностей. Это позволит вынести общие методы соответствующих наследников в
 * родительские классы и с лёгкостью вводить новые сущности.
 */

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

    public function index(string $parentId, string $page): array
    {
        $offset = ((int)$page - 1) * 10;
        $columns = implode(' ,', $this->viewableProperties);
        $query = "SELECT {$columns} FROM {$this->entity}s LIMIT 10 OFFSET {$offset}";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = [
            'total' => $this->getTotalRecords(),
            'offset' => $offset,
            'limit' => 10,
            'items' => []
        ];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $result['items'][] = $row;
        }
        return $result;
    }

    public function show(string $parentId, string $childId = ''): array
    {
        $columns = implode(' ,', $this->viewableProperties);
        $query = "SELECT {$columns} FROM {$this->entity}s WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':id' => $parentId]);
        return [$stmt->fetch(\PDO::FETCH_ASSOC)];
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

    protected function getValue(string $model, string $field, string $conditionKey, string $conditionValue): mixed
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

    protected function getTotalRecords(string $parentId = ''): int
    {
        $query = "SELECT COUNT(*) FROM {$this->entity}s";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
