<?php
// DB helper that supports MySQL (via config.php) or falls back to SQLite.
// Returns a PDO instance.
function getPDO()
{
    $dir = __DIR__;

    // Attempt to load configuration if available
    $configFile = __DIR__ . '/config.php';
    $config = null;
    if (file_exists($configFile)) {
        $cfg = include $configFile;
        if (is_array($cfg)) {
            $config = $cfg;
        }
    }

    if (is_array($config) && isset($config['driver']) && $config['driver'] === 'mysql') {
        $m = $config['mysql'];
        $host = $m['host'] ?? '127.0.0.1';
        $port = $m['port'] ?? 3306;
        $dbname = $m['dbname'] ?? 'nativephp';
        $user = $m['user'] ?? 'root';
        $pass = $m['pass'] ?? '';
        $charset = $m['charset'] ?? 'utf8mb4';

        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $dbname, $charset);
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new PDO($dsn, $user, $pass, $options);

        // Create table if not exists (MySQL syntax)
        $pdo->exec("CREATE TABLE IF NOT EXISTS notes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            content TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET={$charset}");

        return $pdo;
    }

    // Fallback to SQLite
    $dbFile = $dir . '/database.sqlite';
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Enable foreign keys
    $pdo->exec('PRAGMA foreign_keys = ON');
    // Create table if not exists (SQLite)
    $pdo->exec("CREATE TABLE IF NOT EXISTS notes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        content TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    return $pdo;
}

// Simple helper for redirecting
function redirect($url)
{
    header('Location: ' . $url);
    exit;
}
