<?php

namespace books;

require_once __DIR__ . '/../src/Database.php';

use app\Database;

const DB_TYPE = 'mysql';
const MIGRATION_PATH = __DIR__ . "/../migration.sql";
$entity = 'book';

$pdo = Database::get()->connect(DB_TYPE);
Database::get()->migrate($pdo, MIGRATION_PATH);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$method = $_SERVER['REQUEST_METHOD'];
$resource = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

switch ($method) {
    case 'GET':
        // READ
        if (empty($resource[1])) {
            readEntity($pdo, $entity);
        } else {
            readEntitySingle($pdo, $entity, $resource[1]);
        }
        break;
    case 'POST':
        // CREATE
        $layout = ['title', 'author', 'published_at'];
        $data = getData();
        if (compare($layout, $data)) {
            createEntity($pdo, $data, $entity);
        } else {
            sendError();
        }
        break;
    case 'PUT':
        // UPDATE
        $layout = ['id', 'title', 'author', 'published_at'];
        $data = getData();
        if (compare($layout, $data)) {
            updateEntity($pdo, $data, $entity);
        } else {
            sendError();
        }
        break;
    case 'PATCH':
        // UPDATE partial
        $layout = ['id', 'title', 'author', 'published_at'];
        $data = getData();
        $filteredData = array_intersect_key($data, array_flip($layout));
        if (array_key_exists('id', $filteredData) && count($filteredData) > 1) {
            partialUpdateEntity($pdo, $filteredData, $entity);
        } else {
            sendError();
        }
        break;
    case 'DELETE':
        // DELETE
        deleteEntity($pdo, $entity, $resource[1] ?? '');
        break;
    default:
        // Invalid method
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

function validate(mixed $param): mixed
{
    if (is_int($param) || is_string($param)) {
        return $param;
    }
    sendError();
}

function sanitize(int | string $param): string
{
    return htmlspecialchars(strip_tags($param));
}

function compare(array $layout, array $input): bool
{
    return (count($layout) == count($input) && array_diff($layout, array_keys($input)) == []);
}

function sendError(): void
{
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input data']);
    die();
}

function checkId(\PDO $pdo, mixed $id, string $entity): bool
{
    if (is_numeric($id) && $id >= 0 && floor($id) == $id) {
        $query = "SELECT EXISTS (SELECT id FROM {$entity}s WHERE id = :id) AS isExists";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':id' => $id]);
        if (($stmt->fetch())['isExists'] === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'No record with such ID']);
            return false;
        } else {
            return true;
        }
    }
    sendError();
}

function getData(): array
{
    return json_decode(file_get_contents('php://input'), true) ?? [];
}

function readEntity(\PDO $pdo, string $entity): void
{
    $stmt = $pdo->query("SELECT * FROM {$entity}s");
    $entityCount = $pdo->query("SELECT COUNT(*) AS count FROM {$entity}s")->fetch();
    if ($stmt && $entityCount['count'] > 0) {
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        echo json_encode($result);
    } else {
        echo json_encode(['message' => 'No records']);
    }
}

function readEntitySingle(\PDO $pdo, string $entity, string $id): void
{
    $id = sanitize(validate($id));
    if (checkId($pdo, $id, $entity)) {
        $query = "SELECT * FROM {$entity}s WHERE id = :id";
        $stmt = $pdo->prepare($query);
        try {
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch();
            echo json_encode($result);
        } catch (\PDOException $e) {
            sendError();
        }
    }
    die();
}

function createEntity(\PDO $pdo, array $data, string $entity): void
{
    extract(array_map(fn ($param) => sanitize(validate($param)), $data));
    $stmt = $pdo->prepare("INSERT INTO {$entity}s (title, author, published_at) VALUES (?, ?, ?)");
    try {
        if ($stmt->execute([$title, $author, $published_at])) {
            http_response_code(201);
            echo json_encode(['message' => "Done, {$entity} added successfully"]);
        } else {
            sendError();
        }
    } catch (\PDOException $e) {
        sendError();
    }
}

function updateEntity(\PDO $pdo, array $data, string $entity): void
{
    extract(array_map(fn ($param) => sanitize(validate($param)), $data));
    if (checkId($pdo, $id, $entity)) {
        $stmt = $pdo->prepare("UPDATE {$entity}s SET title=?, author=?, published_at=? WHERE id=?");
        try {
            if ($stmt->execute([$title, $author, $published_at, $id])) {
                echo json_encode(['message' => "Done, {$entity} updated successfully"]);
            } else {
                sendError();
            }
        } catch (\PDOException $e) {
            sendError();
        }
    }
    die();
}

function partialUpdateEntity(\PDO $pdo, array $data, string $entity): void
{
    $id = sanitize(validate($data['id']));
    if (checkId($pdo, $id, $entity)) {
        unset($data['id']);
        $query = "UPDATE {$entity}s SET";
        foreach ($data as $key => $value) {
            $query = $query . " {$key} = :{$key},";
        }
        $query = substr($query, 0, -1) . " WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":id", $id);
        foreach ($data as $key => $value) {
            $stmt->bindValue(":{$key}", sanitize(validate($value)));
        }
        try {
            if ($stmt->execute()) {
                echo json_encode(['message' => "Done, {$entity} updated successfully"]);
            } else {
                sendError();
            }
        } catch (\PDOException $e) {
            sendError();
        }
    }
    die();
}

function deleteEntity(\PDO $pdo, string $entity, string $id): void
{
    $id = sanitize(validate($id));
    if (checkId($pdo, $id, $entity)) {
        $stmt = $pdo->prepare("DELETE FROM {$entity}s WHERE id=?");
        try {
            if ($stmt->execute([$id])) {
                echo json_encode(['message' => "Done, {$entity} deleted successfully"]);
            } else {
                sendError();
            }
        } catch (\PDOException $e) {
            sendError();
        }
    }
    die();
}
