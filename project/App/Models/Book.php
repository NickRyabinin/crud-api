<?php

namespace App\Models;

class Book
{
    private $entity = 'book';
    private $properties = ['title', 'author', 'published_at'];
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function store($token, $data)
    {
        $hashedToken = base64_decode($token);
        if ($this->compare($this->properties, $data)) {
            if ($this->checkToken($hashedToken)) {
                try {
                    $query = "INSERT INTO {$this->entity}s (title, author, published_at)
                        VALUES (:title, :author, :published_at)";
                    $stmt = $this->pdo->prepare($query);
                    foreach ($data as $key => $value) {
                        $stmt->bindValue(":{$key}", $value);
                    }
                    $stmt->execute();
                    return true;
                } catch (\PDOException $e) {
                    return false;
                }
            } else {
                return '';
            }
        }
        return false;
    }

    public function index()
    {
        $query = "SELECT * FROM {$this->entity}s";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function show($id)
    {
        $query = "SELECT * FROM {$this->entity}s WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function update($id, $token, $data)
    {
        $hashedToken = base64_decode($token);
        $filteredData = array_intersect_key($data, array_flip($this->properties));
        if ($this->checkId($id)) {
            if ($this->checkToken($hashedToken)) {
                if (count($filteredData) > 0) {
                    $query = "UPDATE {$this->entity}s SET";
                    foreach ($filteredData as $key => $value) {
                        $query = $query . " {$key} = :{$key},";
                    }
                    $query = substr($query, 0, -1) . " WHERE id = :id";
                    $stmt = $this->pdo->prepare($query);
                    $stmt->bindParam(":id", $id);
                    foreach ($filteredData as $key => $value) {
                        $stmt->bindValue(":{$key}", $value);
                    }
                    try {
                        if ($stmt->execute()) {
                            return true;
                        }
                    } catch (\PDOException $e) {
                        return null;
                    }
                }
                return null;
            }
            return '';
        }
        return false;
    }

    public function destroy($id, $token)
    {
        $hashedToken = base64_decode($token);
        if ($this->checkId($id)) {
            if ($this->checkToken($hashedToken)) {
                $query = "DELETE FROM {$this->entity}s WHERE id= :id";
                $stmt = $this->pdo->prepare($query);
                $stmt->execute([':id' => $id]);
                return true;
            }
            return '';
        }
        return false;
    }

    private function checkId($id)
    {
        $query = "SELECT EXISTS (SELECT id FROM {$this->entity}s WHERE id = :id) AS isExists";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':id' => $id]);
        return (($stmt->fetch())['isExists'] === 0) ? false : true;
    }

    private function checkToken($hashedToken)
    {
        $query = "SELECT EXISTS (SELECT id FROM users WHERE hashed_token = :hashed_token) AS isExists";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':hashed_token' => $hashedToken]);
        return (($stmt->fetch())['isExists'] === 0) ? false : true;
    }

    private function compare(array $properties, array $input): bool
    {
        return (count($properties) === count($input) && array_diff($properties, array_keys($input)) === []);
    }
}
