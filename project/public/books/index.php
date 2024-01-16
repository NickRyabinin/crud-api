<?php

require_once __DIR__ . '/../../App/Core/Container.php';
require_once __DIR__ . '/../../App/Core/Database.php';
require_once __DIR__ . '/../../App/Core/Helper.php';
require_once __DIR__ . '/../../App/Routers/BookRouter.php';
require_once __DIR__ . '/../../App/Models/Book.php';
require_once __DIR__ . '/../../App/Controllers/BookController.php';
require_once __DIR__ . '/../../App/Views/BookView.php';

use App\Core\Container;
use App\Core\Database;
use App\Core\Helper;
use App\Routers\BookRouter;
use App\Models\Book;
use App\Controllers\BookController;
use App\Views\BookView;

const DB_TYPE = 'mysql';
const MIGRATION_PATH = __DIR__ . "/../../App/Database/Migrations/migration.sql";

$pdo = Database::get()->connect(DB_TYPE);
Database::get()->migrate($pdo, MIGRATION_PATH);

$container = new Container();
$container->set('pdo', $pdo);
$container->set('helper', new Helper());
$container->set('book', new Book($container->get('pdo')));
$container->set('bookView', new BookView());
$container->set('bookController', new BookController(
    $container->get('book'),
    $container->get('bookView'),
    $container->get('helper')
));

$router = new BookRouter($container);
$router->route();
