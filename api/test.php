<?php
/**
 * DayTrack – API Debug & Health Check
 * Akses: http://localhost/apptodolist/api/test.php
 * HAPUS file ini setelah production!
 */
header('Content-Type: application/json');

$result = [
    'php_version'   => PHP_VERSION,
    'php_ok'        => version_compare(PHP_VERSION, '7.4.0', '>='),
    'session'       => session_status(),
    'db_connect'    => false,
    'db_error'      => null,
    'tables'        => [],
    'user_count'    => 0,
];

// Test DB
try {
    require_once __DIR__ . '/../config/database.php';
    $db = getDB();
    $result['db_connect'] = true;

    // List tables
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $result['tables'] = $tables;

    // Count users
    if (in_array('users', $tables)) {
        $result['user_count'] = (int)$db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    }
} catch (Exception $e) {
    $result['db_error'] = $e->getMessage();
}

echo json_encode($result, JSON_PRETTY_PRINT);
