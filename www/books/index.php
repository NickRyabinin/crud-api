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
        // Read
        $stmt = $pdo->query('SELECT * FROM books');
        $booksCount = $pdo->query('SELECT COUNT(*) AS count FROM books')->fetch();
        if ($stmt && $booksCount['count'] > 0) {
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            echo json_encode($result);
        } else {
            echo json_encode(['message' => 'No records found']);
        }
        break;
    case 'POST':
        // Create
        $layout = ['title', 'author', 'published_at'];
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        if (compare($layout, $data)) {
            $title = sanitize($data['title']);
            $author = sanitize($data['author']);
            $published_at = sanitize($data['published_at']);

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
        } else {
            sendError();
        }
        break;
    case 'PUT':
        // Update
        $layout = ['id', 'title', 'author', 'published_at'];
        $data = json_decode(file_get_contents("php://input"), true) ?? [];
        if (compare($layout, $data)) {
            $id = sanitize($data['id']);
            $title = sanitize($data['title']);
            $author = sanitize($data['author']);
            $published_at = sanitize($data['published_at']);

            $stmt = $pdo->prepare('UPDATE books SET title=?, author=?, published_at=? WHERE id=?');
            try {
                if ($stmt->execute([$title, $author, $published_at, $id])) {
                    echo json_encode(['message' => 'Book updated successfully']);
                } else {
                    sendError();
                }
            } catch (\PDOException $e) {
                sendError();
            }
        } else {
            sendError();
        }
        break;
    case 'DELETE':
        // Delete
        $layout = ['id'];
        $data = json_decode(file_get_contents("php://input"), true) ?? [];
        if (compare($layout, $data)) {
            $id = sanitize($data['id']);

            $stmt = $pdo->prepare('DELETE FROM books WHERE id=?');
            try {
                if ($stmt->execute([$id])) {
                    echo json_encode(['message' => 'Book deleted successfully']);
                } else {
                    sendError();
                }
            } catch (\PDOException $e) {
                sendError();
            }
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

function sanitize($param)
{
    return htmlspecialchars(strip_tags($param));
}

function compare(array $layout, array $input): bool
{
    return (count($layout) == count($input) && array_diff($layout, array_keys($input)) == []);
}

function sendError()
{
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input JSON data']);
}
