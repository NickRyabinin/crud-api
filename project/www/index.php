<?php

spl_autoload_register(function ($className) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $className) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Core\Container;
use App\Core\Database;
use App\Core\Helper;
use App\Routers\Router;
use App\Models\Book;
use App\Models\User;
use App\Models\Opinion;
use App\Controllers\BookController;
use App\Controllers\UserController;
use App\Controllers\HomeController;
use App\Controllers\OpinionController;
use App\Controllers\ExceptionController;
use App\Views\View;
use App\Views\HomeView;

const DB_TYPE = 'mysql';
const MIGRATION_PATH = __DIR__ . "/../App/Database/Migrations/migration.sql";

$pdo = Database::get()->connect(DB_TYPE);
Database::get()->migrate($pdo, MIGRATION_PATH);

$container = new Container();
$container->set('pdo', $pdo);
$container->set('helper', new Helper());
$container->set('book', new Book($container->get('pdo')));
$container->set('user', new User($container->get('pdo')));
$container->set('opinion', new Opinion(
    $container->get('pdo'),
    $container->get('book')
));
$container->set('View', new View());
$container->set('homeView', new HomeView());
$container->set('bookController', new BookController(
    $container->get('book'),
    $container->get('View'),
    $container->get('helper')
));
$container->set('userController', new UserController(
    $container->get('user'),
    $container->get('View'),
    $container->get('helper')
));
$container->set('homeController', new HomeController(
    $container->get('homeView')
));
$container->set('opinionController', new OpinionController(
    $container->get('opinion'),
    $container->get('View'),
    $container->get('helper')
));
$container->set('exceptionController', new ExceptionController(
    $container->get('View')
));

$router = new Router($container);
$router->route();
