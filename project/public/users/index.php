<?php

require_once __DIR__ . '/../../App/Core/Container.php';
require_once __DIR__ . '/../../App/Core/Database.php';
require_once __DIR__ . '/../../App/Core/Helper.php';
require_once __DIR__ . '/../../App/Routers/Router.php';
require_once __DIR__ . '/../../App/Models/User.php';
require_once __DIR__ . '/../../App/Controllers/UserController.php';
require_once __DIR__ . '/../../App/Views/View.php';

use App\Core\Container;
use App\Core\Database;
use App\Core\Helper;
use App\Routers\Router;
use App\Models\User;
use App\Controllers\UserController;
use App\Views\View;

const DB_TYPE = 'mysql';
const MIGRATION_PATH = __DIR__ . "/../../App/Database/Migrations/migration.sql";

$pdo = Database::get()->connect(DB_TYPE);
Database::get()->migrate($pdo, MIGRATION_PATH);

$container = new Container();
$container->set('pdo', $pdo);
$container->set('helper', new Helper());
$container->set('user', new User($container->get('pdo')));
$container->set('View', new View());
$container->set('userController', new UserController(
    $container->get('user'),
    $container->get('View'),
    $container->get('helper')
));

$router = new Router($container);
$router->route();
