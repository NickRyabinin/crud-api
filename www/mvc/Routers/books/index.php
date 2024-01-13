<?php

namespace books;

require_once __DIR__ . '/../../../src/Database.php';
require_once __DIR__ . '/../../Models/BookModel.php';
require_once __DIR__ . '/../../Controllers/BookController.php';
require_once __DIR__ . '/../../Views/BookView.php';

use app\Database;

const DB_TYPE = 'mysql';
const MIGRATION_PATH = __DIR__ . "/../../../migration.sql";

$pdo = Database::get()->connect(DB_TYPE);
Database::get()->migrate($pdo, MIGRATION_PATH);

$model = new BookModel($pdo);
$controller = new BookController($model);
$view = new BookView();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $data = $controller->read();
        break;
    case 'POST':
        $data = $controller->create();
        break;
    case 'PUT':
    case 'PATCH':
        $data = $controller->update();
        break;
    case 'DELETE':
        $data = $controller->delete();
        break;
    default:
        // Invalid method
        $data = $controller->invalidMethod();
        break;
}

$view->send($data);
