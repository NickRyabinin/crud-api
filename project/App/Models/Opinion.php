<?php

namespace App\Models;

use App\Core\Exceptions\InvalidDataException;

class Opinion extends Model
{
    public string $entity = 'opinion';
    protected array $properties = ['opinion', 'book_id', 'author_login', 'opinion_id'];
    protected array $fillableProperties = ['opinion'];
    public array $belongsTo = ['book'];
    protected \PDO $pdo;
    protected Book $book;

    public function __construct(\PDO $pdo, Book $book)
    {
        $this->pdo = $pdo;
        $this->book = $book;
    }

    public function store(string $parentId, string $token, array $data): bool
    {
        parent::checkToken($token);
        $this->book->checkId($parentId);
        parent::compare($this->fillableProperties, $data);
        $data['book_id'] = $parentId;
        $data['author_login'] = parent::get('user', 'login', 'token', $token);
        $data['opinion_id'] = $this->getMaxChildId($parentId) + 1;
        $query = "INSERT INTO {$this->entity}s (opinion, book_id, author_login, opinion_id)
            VALUES (:opinion, :book_id, :author_login, :opinion_id)";
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
        $query = "SELECT * FROM {$this->entity}s WHERE book_id = :book_id AND opinion_id = :opinion_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':book_id' => $parentId, ':opinion_id' => $childId]);
        return $stmt->fetch();
    }

    public function update(string $parentId, string $childId, string $token, array $data): bool
    {
        parent::checkToken($token);
        $this->book->checkId($parentId);
        parent::checkId($childId);
        $filteredData = array_intersect_key($data, array_flip($this->fillableProperties));
        if (count($filteredData) === 0) {
            throw new InvalidDataException();
        }
        $filteredData['author_login'] = parent::get('user', 'login', 'token', $token);
        $query = "UPDATE {$this->entity}s SET";
        foreach ($filteredData as $key => $value) {
            $query = $query . " {$key} = :{$key},";
        }
        $query = rtrim($query, ',') . " WHERE book_id = :book_id AND opinion_id = :opinion_id";
        try {
            $stmt = $this->pdo->prepare($query);
            foreach ($filteredData as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }
            $stmt->bindValue(":book_id", $parentId);
            $stmt->bindValue(":opinion_id", $childId);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new InvalidDataException();
        }
        return true;
    }

    public function destroy(string $parentId, string $childId, string $token): bool
    {
        parent::checkToken($token);
        $this->book->checkId($parentId);
        parent::checkId($childId);
        $query = "DELETE FROM {$this->entity}s WHERE book_id = :book_id AND opinion_id = :opinion_id";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([":book_id" => $parentId, ":opinion_id" => $childId]);
        } catch (\PDOException $e) {
            throw new InvalidDataException();
        }
        return true;
    }

    protected function getMaxChildId(string $parentId): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(MAX(opinion_id), 0) as max_opinion_id FROM opinions WHERE book_id = :book_id"
        );
        $stmt->bindParam(':book_id', $parentId);
        $stmt->execute();
        return $stmt->fetch()['max_opinion_id'];
    }
}
