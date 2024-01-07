<?php

namespace books;

require_once __DIR__ . '/../src/Database.php';

use app\Database;

const DB_TYPE = 'mysql';
const MIGRATION_PATH = __DIR__ . "/../migration.sql";

$pdo = Database::get()->connect(DB_TYPE);
Database::get()->migrate($pdo, MIGRATION_PATH);

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // READ
        readEntity($pdo);
        break;
    case 'POST':
        // CREATE
        $layout = ['title', 'author', 'published_at'];
        $data = getData();
        if (compare($layout, $data)) {
            createEntity($pdo, $data);
        } else {
            sendError();
        }
        break;
    case 'PUT':
        // UPDATE
        $layout = ['id', 'title', 'author', 'published_at'];
        $data = getData();
        if (compare($layout, $data)) {
            updateEntity($pdo, $data);
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
            partialUpdateEntity($pdo, $filteredData);
        } else {
            sendError();
        }
        break;
    case 'DELETE':
        // DELETE
        $layout = ['id'];
        $data = getData();
        if (compare($layout, $data)) {
            deleteEntity($pdo, $data);
        } else {
            sendError();
        }
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
    echo json_encode(['error' => 'Invalid input JSON data']);
    die();
}

function checkId(\PDO $pdo, mixed $id): bool
{
    $query = "SELECT EXISTS (SELECT id FROM books WHERE id = {$id}) AS isExists";
    return (int)($id) == $id ? (bool)($pdo->query($query)->fetch())['isExists'] : false;
}

function getData(): array
{
    return json_decode(file_get_contents('php://input'), true) ?? [];
}

function readEntity(\PDO $pdo): void
{
    $stmt = $pdo->query('SELECT * FROM books');
    $booksCount = $pdo->query('SELECT COUNT(*) AS count FROM books')->fetch();
    if ($stmt && $booksCount['count'] > 0) {
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        echo json_encode($result);
    } else {
        echo json_encode(['message' => 'No records']);
    }
}

function createEntity(\PDO $pdo, array $data): void
{
    extract(array_map(fn ($param) => sanitize(validate($param)), $data));
    $stmt = $pdo->prepare('INSERT INTO books (title, author, published_at) VALUES (?, ?, ?)');
    try {
        if ($stmt->execute([$title, $author, $published_at])) {
            http_response_code(201);
            echo json_encode(['message' => 'Book added successfully']);
        } else {
            sendError();
        }
    } catch (\PDOException $e) {
        sendError();
    }
}

function updateEntity(\PDO $pdo, array $data): void
{
    extract(array_map(fn ($param) => sanitize(validate($param)), $data));
    if (checkId($pdo, $id)) {
        $stmt = $pdo->prepare('UPDATE books SET title=?, author=?, published_at=? WHERE id=?');
    } else {
        echo json_encode(['error' => 'No record with such ID']);
        die();
    }
    try {
        if ($stmt->execute([$title, $author, $published_at, $id])) {
            echo json_encode(['message' => 'Book updated successfully']);
        } else {
            sendError();
        }
    } catch (\PDOException $e) {
        sendError();
    }
}

function partialUpdateEntity(\PDO $pdo, array $data): void
{
    $id = sanitize(validate($data['id']));
    if (checkId($pdo, $id)) {
        unset($data['id']);
        $query = 'UPDATE books SET';
        foreach ($data as $key => $value) {
            $query = $query . " {$key}=:{$key},";
        }
        $query = substr($query, 0, -1) . " WHERE id=:id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":id", $id);
        foreach ($data as $key => $value) {
            $stmt->bindParam(":{$key}", sanitize(validate($value)));
        }
        try {
            if ($stmt->execute()) {
                echo json_encode(['message' => 'Book updated successfully']);
            } else {
                sendError();
            }
        } catch (\PDOException $e) {
            sendError();
        }
    } else {
        echo json_encode(['error' => 'No record with such ID']);
        die();
    }
}

function deleteEntity(\PDO $pdo, array $data): void
{
    extract(array_map(fn ($param) => sanitize(validate($param)), $data));
    if (checkId($pdo, $id)) {
        $stmt = $pdo->prepare('DELETE FROM books WHERE id=?');
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'No record with such ID']);
        die();
    }
    try {
        if ($stmt->execute([$id])) {
            echo json_encode(['message' => 'Book deleted successfully']);
        } else {
            sendError();
        }
    } catch (\PDOException $e) {
        sendError();
    }
}
