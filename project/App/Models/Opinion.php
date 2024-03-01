<?php

namespace App\Models;

use App\Core\Exceptions\InvalidDataException;

class Opinion extends Model
{
    public string $entity = 'opinion';
    protected array $properties = ['opinion', 'book_id', 'author_login', 'opinion_id'];
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
        $data['book_id'] = $parentId;
        $data['author_login'] = parent::get('user', 'login', 'token', $token);
        $data['opinion_id'] = $this->getMaxChildId($parentId) + 1;
        parent::compare($this->properties, $data);
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

    public function destroy(string $parentId, string $childId, string $token): bool
    {
        parent::checkId($childId);
        $this->book->checkId($parentId);
        parent::checkToken($token);
        $query = "DELETE FROM {$this->entity}s WHERE book_id = :book_id AND opinion_id = :opinion_id";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([":book_id" => $parentId, ":opinion_id" => $childId]);
        } catch (\PDOException $e) {
            throw new InvalidDataException();
        }
        return true;
    }

    protected function getMaxChildId($parentId)
    {
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(MAX(opinion_id), 0) as max_opinion_id FROM opinions WHERE book_id = :book_id"
        );
        $stmt->bindParam(':book_id', $parentId);
        $stmt->execute();
        return $stmt->fetch()['max_opinion_id'];
    }
}
