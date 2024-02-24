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
}
