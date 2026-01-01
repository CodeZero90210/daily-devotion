<?php // db.php
require_once __DIR__ . '/../config/config.php';

function getDatabaseConnection (){
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    global $dsn, $user, $pass;

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

    } catch (PDOException $e) {
        throw new RuntimeException('DB connection failed: ' . $e->getMessage(), 0, $e);
    }

    return $pdo;
}