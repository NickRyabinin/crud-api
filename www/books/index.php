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
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        echo json_encode($result);
    break;
    case 'POST':
    // Create
        $data = json_decode(file_get_contents('php://input'), true);
        $title = $data['title'];
        $author = $data['author'];
        $published_at = $data['published_at'];

        $stmt = $pdo->prepare('INSERT INTO books (title, author, published_at) VALUES (?, ?, ?)');
        $stmt->execute([$title, $author, $published_at]);

        http_response_code(201);
        echo json_encode(['message' => 'Book added successfully']);
    break;
    case 'PUT':
    // Update
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'];
        $title = $data['title'];
        $author = $data['author'];
        $published_at = $data['published_at'];

        $stmt = $pdo->prepare('UPDATE books SET title=?, author=?, published_at=? WHERE id=?');
        $stmt->execute([$title, $author, $published_at, $id]);

        echo json_encode(['message' => 'Book updated successfully']);
    break;
    case 'DELETE':
    // Delete
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'];

        $stmt = $pdo->prepare('DELETE FROM books WHERE id=?');
        $stmt->execute([$id]);

        echo json_encode(['message' => 'Book deleted successfully']);
    break;
    default:
     // Invalid method
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    break;
}
