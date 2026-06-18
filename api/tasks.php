<?php
/**
 * DayTrack – Tasks API
 * api/tasks.php
 *
 * GET              → list all tasks for logged-in user
 * POST             → create new task
 * PUT  ?id=X       → update task
 * DELETE ?id=X     → delete task
 */

ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();
ob_clean();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$apiMode = true;
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$userId = (int)$currentUser['id'];

// Parse body
$raw    = file_get_contents('php://input');
$decoded = $raw ? json_decode($raw, true) : null;
$input  = is_array($decoded) ? $decoded : [];
$data   = array_merge($_POST, $input);

/* ── Helper ── */
function jsonOk($p = []) { echo json_encode(array_merge(['success' => true], $p)); exit; }
function jsonErr($m, $c = 400) { http_response_code($c); echo json_encode(['success' => false, 'error' => $m]); exit; }

$db = getDB();

switch ($method) {

    /* ── READ ── */
    case 'GET':
        $stmt = $db->prepare(
            'SELECT id, project_name AS project, title, done, priority,
                    DATE_FORMAT(due_date, "%Y-%m-%d") AS due, notes,
                    created_at
             FROM tasks WHERE user_id = ? ORDER BY created_at DESC'
        );
        $stmt->execute([$userId]);
        $tasks = $stmt->fetchAll();
        // Cast booleans
        foreach ($tasks as &$t) $t['done'] = (bool)$t['done'];
        jsonOk(['data' => $tasks]);

    /* ── CREATE ── */
    case 'POST':
        $title   = trim($data['title']    ?? '');
        $project = trim($data['project']  ?? 'General');
        $priority= in_array($data['priority'] ?? '', ['low','medium','high']) ? $data['priority'] : 'medium';
        $due     = !empty($data['due'])   ? $data['due']   : null;
        $notes   = trim($data['notes']    ?? '');

        if (!$title) jsonErr('Title is required.');

        $stmt = $db->prepare(
            'INSERT INTO tasks (user_id, project_name, title, priority, due_date, notes)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$userId, $project, $title, $priority, $due, $notes]);
        $newId = $db->lastInsertId();

        $row = $db->prepare('SELECT id, project_name AS project, title, done, priority,
                                    DATE_FORMAT(due_date,"%Y-%m-%d") AS due, notes, created_at
                             FROM tasks WHERE id = ?');
        $row->execute([$newId]);
        $task = $row->fetch();
        $task['done'] = (bool)$task['done'];
        jsonOk(['data' => $task]);

    /* ── UPDATE ── */
    case 'PUT':
        if (!$id) jsonErr('Task ID required.');

        // Check ownership
        $own = $db->prepare('SELECT id FROM tasks WHERE id = ? AND user_id = ?');
        $own->execute([$id, $userId]);
        if (!$own->fetch()) jsonErr('Task not found.', 404);

        $fields = [];
        $params = [];

        if (isset($data['title']))    { $fields[] = 'title = ?';        $params[] = trim($data['title']); }
        if (isset($data['project']))  { $fields[] = 'project_name = ?'; $params[] = trim($data['project']); }
        if (isset($data['priority'])) { $fields[] = 'priority = ?';     $params[] = $data['priority']; }
        if (isset($data['due']))      { $fields[] = 'due_date = ?';     $params[] = $data['due'] ?: null; }
        if (isset($data['notes']))    { $fields[] = 'notes = ?';        $params[] = trim($data['notes']); }
        if (isset($data['done']))     { $fields[] = 'done = ?';         $params[] = $data['done'] ? 1 : 0; }

        if (!$fields) jsonErr('Nothing to update.');

        $params[] = $id;
        $db->prepare('UPDATE tasks SET ' . implode(', ', $fields) . ' WHERE id = ?')
           ->execute($params);

        $row = $db->prepare('SELECT id, project_name AS project, title, done, priority,
                                    DATE_FORMAT(due_date,"%Y-%m-%d") AS due, notes, created_at
                             FROM tasks WHERE id = ?');
        $row->execute([$id]);
        $task = $row->fetch();
        $task['done'] = (bool)$task['done'];
        jsonOk(['data' => $task]);

    /* ── DELETE ── */
    case 'DELETE':
        if (!$id) jsonErr('Task ID required.');

        $own = $db->prepare('SELECT id FROM tasks WHERE id = ? AND user_id = ?');
        $own->execute([$id, $userId]);
        if (!$own->fetch()) jsonErr('Task not found.', 404);

        $db->prepare('DELETE FROM tasks WHERE id = ?')->execute([$id]);
        jsonOk(['message' => 'Task deleted.']);

    default:
        jsonErr('Method not allowed.', 405);
}
