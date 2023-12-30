<?php

require_once __DIR__ . '/templates/main.phtml';
require_once __DIR__ . '/src/Database.php';

use app\Database;

$pdo = Database::get()->connect();
echo "I'm not running yet...";
