<?php

namespace App\Models;

class User
{
    private $entity = 'user';
    private $properties = ['login', 'email', 'hashed_token'];
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function store($data)
    {
        if ($this->compare($this->properties, $data)) {
            try {
                $query = "INSERT INTO {$this->entity}s (login, email, hashed_token)
                    VALUES (:login, :email, :hashed_token)";
                $stmt = $this->pdo->prepare($query);
                foreach ($data as $key => $value) {
                    $stmt->bindValue(":{$key}", $value);
                }
                $stmt->execute();
                return true;
            } catch (\PDOException $e) {
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

    public function update($id, $data)
    {
        $filteredData = array_intersect_key($data, array_flip($this->properties));
        if ($this->checkId($id)) {
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
                }
            }
            return null;
        }
        return false;
    }

    public function destroy($id)
    {
        if ($this->checkId($id)) {
            $query = "DELETE FROM {$this->entity}s WHERE id= :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':id' => $id]);
            return true;
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

    private function compare(array $properties, array $input): bool
    {
        return (count($properties) === count($input) && array_diff($properties, array_keys($input)) === []);
    }
}
