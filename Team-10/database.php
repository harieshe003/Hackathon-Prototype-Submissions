<?php
// config/database.php

require_once __DIR__ . '/config.php';

class Database {
    private static ?PDO $instance = null;

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $driver = getenv('DB_DRIVER') ?: 'sqlite';
            
            try {
                if ($driver === 'mysql') {
                    $host = getenv('DB_HOST') ?: '127.0.0.1';
                    $port = getenv('DB_PORT') ?: '3306';
                    $db   = getenv('DB_NAME') ?: 'ecolens';
                    $user = getenv('DB_USER') ?: 'root';
                    $pass = getenv('DB_PASSWORD') ?: '';
                    
                    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
                    self::$instance = new PDO($dsn, $user, $pass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]);
                } else {
                    // Default to SQLite
                    $sqlitePath = ROOT_PATH . '/' . (getenv('DB_SQLITE_PATH') ?: 'database/ecolens.sqlite');
                    $dir = dirname($sqlitePath);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    
                    $isNew = !file_exists($sqlitePath);
                    self::$instance = new PDO("sqlite:{$sqlitePath}", null, null, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]);

                    // If new database or missing tables, run schema & seed
                    self::ensureTablesExist(self::$instance, $isNew);
                }
            } catch (PDOException $e) {
                // Fallback to SQLite if MySQL connection fails
                if ($driver === 'mysql') {
                    $sqlitePath = ROOT_PATH . '/database/ecolens.sqlite';
                    $dir = dirname($sqlitePath);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    $isNew = !file_exists($sqlitePath);
                    self::$instance = new PDO("sqlite:{$sqlitePath}", null, null, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]);
                    self::ensureTablesExist(self::$instance, $isNew);
                } else {
                    die("Database Connection Error: " . $e->getMessage());
                }
            }
        }
        return self::$instance;
    }

    private static function ensureTablesExist(PDO $db, bool $forceSeed = false): void {
        // Check if users table exists
        $check = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'")->fetch();
        if (!$check || $forceSeed) {
            $sqlFile = ROOT_PATH . '/database/ecolens.sql';
            if (file_exists($sqlFile)) {
                $sql = file_get_contents($sqlFile);
                // Convert MySQL syntax tweaks for SQLite compatibility if needed
                $sqliteSql = str_replace('AUTO_INCREMENT', '', $sql);
                $sqliteSql = str_replace('INT ', 'INTEGER ', $sqliteSql);
                $sqliteSql = str_replace('TINYINT(1)', 'INTEGER', $sqliteSql);
                $sqliteSql = str_replace('ON UPDATE CURRENT_TIMESTAMP', '', $sqliteSql);
                
                $db->exec($sqliteSql);
            }
        } else {
            // Check if is_demo column exists in products table
            try {
                $db->query("SELECT is_demo FROM products LIMIT 1");
            } catch (Exception $e) {
                $db->exec("ALTER TABLE products ADD COLUMN is_demo INTEGER DEFAULT 0");
            }
        }
    }
}
