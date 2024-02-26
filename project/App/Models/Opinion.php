<?php

namespace App\Models;

use App\Core\Exceptions\InvalidDataException;

class Opinion extends Model
{
    public string $entity = 'opinion';
    protected array $properties = ['opinion'];
    protected \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }
}
