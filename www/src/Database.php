<?php

namespace app;

final class Database
{
    private static ?Database $connection = null;

    protected function __construct()
    {
    }

    public function connect(string $dbType)
    {
        $databaseUrl = parse_url((string) getenv('DATABASE_URL'));
        $username = $databaseUrl['user'] ?? '';
        $password = $databaseUrl['pass'] ?? '';
        $host = $databaseUrl['host'] ?? '';
        $port = $databaseUrl['port'] ?? '';
        $dbName = ltrim($databaseUrl['path'], '/');
        $dsn = "{$dbType}:host={$host};port={$port};dbname={$dbName}";
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
          ];
        try {
            $pdo = new \PDO($dsn, $username, $password, $options);
        } catch (\PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
        return $pdo;
    }

    public function migrate(\PDO $pdo, string $migrationPath)
    {
        try {
            $migration = file_get_contents($migrationPath);
            $statements = explode("\r\n\r\n", $migration);
            foreach ($statements as $statement) {
                $pdo->exec($statement);
            }
        } catch (\PDOException $e) {
            die("Database migration failed: " . $e->getMessage());
        }
        return $pdo;
    }

    public static function get()
    {
        if (static::$connection === null) {
            static::$connection = new self();
        }
        return static::$connection;
    }
}
