<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();
ob_clean();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$apiMode = true;
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];
$userId = (int)$currentUser['id'];
$userName = $currentUser['name'];

$raw    = file_get_contents('php://input');
$decoded = $raw ? json_decode($raw, true) : null;
$input  = is_array($decoded) ? $decoded : [];
$data   = array_merge($_POST, $input);

function jsonOk($p = []) { echo json_encode(array_merge(['success' => true], $p)); exit; }
function jsonErr($m, $c = 400) { http_response_code($c); echo json_encode(['success' => false, 'error' => $m]); exit; }

$db = getDB();

switch ($method) {
    case 'GET':
        $stmt = $db->query('SELECT * FROM messages ORDER BY created_at ASC');
        $rows = $stmt->fetchAll();
        jsonOk(['data' => $rows]);

    case 'POST':
        $body = trim($data['body'] ?? '');
        if (!$body) jsonErr('Message cannot be empty.');

        $stmt = $db->prepare('INSERT INTO messages (user_id, sender_name, body) VALUES (?, ?, ?)');
        $stmt->execute([$userId, $userName, $body]);
        $newId = $db->lastInsertId();

        $row = $db->prepare('SELECT * FROM messages WHERE id = ?');
        $row->execute([$newId]);
        jsonOk(['data' => $row->fetch()]);

    default:
        jsonErr('Method not allowed.', 405);
}
