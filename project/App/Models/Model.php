<?php

namespace App\Models;

abstract class Model
{
    protected $pdo;
    public $entity;

    public function __toString()
    {
        return $this->entity;
    }

    protected function checkId(string $id): bool
    {
        $query = "SELECT EXISTS (SELECT id FROM {$this->entity}s WHERE id = :id) AS isExists";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':id' => $id]);
        return (($stmt->fetch())['isExists'] === 0) ? false : true;
    }

    protected function checkToken(string $hashedToken): bool
    {
        $query = "SELECT EXISTS (SELECT id FROM users WHERE hashed_token = :hashed_token) AS isExists";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':hashed_token' => $hashedToken]);
        return (($stmt->fetch())['isExists'] === 0) ? false : true;
    }

    protected function compare(array $properties, array $input): bool
    {
        return (count($properties) === count($input) && array_diff($properties, array_keys($input)) === []);
    }
}
