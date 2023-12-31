<?php

namespace web;

require_once __DIR__ . '/templates/main.phtml';
require_once __DIR__ . '/src/Database.php';

use app\Database;

const DB_TYPE = 'mysql';
const MIGRATION_PATH = __DIR__ . "/migration.sql";

$pdo = Database::get()->connect(DB_TYPE);
Database::get()->migrate($pdo, MIGRATION_PATH);
echo "I'm not running yet...";
