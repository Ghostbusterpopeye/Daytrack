<?php
/**
 * DayTrack – Projects API
 * api/projects.php
 *
 * GET              → list all projects for logged-in user
 * POST             → create new project
 * PUT  ?id=X       → update project
 * DELETE ?id=X     → delete project
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

$raw    = file_get_contents('php://input');
$decoded = $raw ? json_decode($raw, true) : null;
$input  = is_array($decoded) ? $decoded : [];
$data   = array_merge($_POST, $input);

function jsonOk($p = []) { echo json_encode(array_merge(['success' => true], $p)); exit; }
function jsonErr($m, $c = 400) { http_response_code($c); echo json_encode(['success' => false, 'error' => $m]); exit; }

$db = getDB();

switch ($method) {

    /* ── READ ── */
    case 'GET':
        $stmt = $db->prepare(
            'SELECT p.id, p.name, p.color, p.icon, p.description AS `desc`,
                    p.members, p.progress, p.archived, p.created_at,
                    COUNT(t.id)              AS total_tasks,
                    SUM(t.done = 1)          AS done_tasks
             FROM projects p
             LEFT JOIN tasks t ON t.user_id = p.user_id AND t.project_name = p.name
             WHERE p.user_id = ?
             GROUP BY p.id
             ORDER BY p.created_at DESC'
        );
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll();
        foreach ($rows as &$p) {
            $p['archived']    = (bool)$p['archived'];
            $p['total_tasks'] = (int)$p['total_tasks'];
            $p['done_tasks']  = (int)$p['done_tasks'];
            // Re-calculate progress from real task data
            if ($p['total_tasks'] > 0) {
                $p['progress'] = (int)round($p['done_tasks'] / $p['total_tasks'] * 100);
            }
            $p['label'] = $p['archived'] ? 'Archived' : $p['progress'] . '% Complete';
        }
        jsonOk(['data' => $rows]);

    /* ── CREATE ── */
    case 'POST':
        $name    = trim($data['name']    ?? '');
        $color   = trim($data['color']   ?? 'primary');
        $icon    = trim($data['icon']    ?? 'bi-briefcase');
        $desc    = trim($data['desc']    ?? '');
        $members = max(1, (int)($data['members'] ?? 1));

        if (!$name) jsonErr('Project name is required.');

        $stmt = $db->prepare(
            'INSERT INTO projects (user_id, name, color, icon, description, members)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$userId, $name, $color, $icon, $desc, $members]);
        $newId = $db->lastInsertId();

        $row = $db->prepare('SELECT * FROM projects WHERE id = ?');
        $row->execute([$newId]);
        $proj = $row->fetch();
        $proj['archived'] = (bool)$proj['archived'];
        $proj['label']    = '0% Complete';
        $proj['desc']     = $proj['description'];
        jsonOk(['data' => $proj]);

    /* ── UPDATE ── */
    case 'PUT':
        if (!$id) jsonErr('Project ID required.');

        $own = $db->prepare('SELECT id FROM projects WHERE id = ? AND user_id = ?');
        $own->execute([$id, $userId]);
        if (!$own->fetch()) jsonErr('Project not found.', 404);

        $fields = [];
        $params = [];

        if (isset($data['name']))     { $fields[] = 'name = ?';        $params[] = trim($data['name']); }
        if (isset($data['color']))    { $fields[] = 'color = ?';       $params[] = $data['color']; }
        if (isset($data['icon']))     { $fields[] = 'icon = ?';        $params[] = $data['icon']; }
        if (isset($data['desc']))     { $fields[] = 'description = ?'; $params[] = trim($data['desc']); }
        if (isset($data['members']))  { $fields[] = 'members = ?';     $params[] = max(1,(int)$data['members']); }
        if (isset($data['archived'])) { $fields[] = 'archived = ?';    $params[] = $data['archived'] ? 1 : 0; }
        if (isset($data['progress'])) { $fields[] = 'progress = ?';    $params[] = (int)$data['progress']; }

        if (!$fields) jsonErr('Nothing to update.');

        $params[] = $id;
        $db->prepare('UPDATE projects SET ' . implode(', ', $fields) . ' WHERE id = ?')
           ->execute($params);

        $row = $db->prepare(
            'SELECT p.*, p.description AS `desc`,
                    COUNT(t.id) AS total_tasks, SUM(t.done=1) AS done_tasks
             FROM projects p
             LEFT JOIN tasks t ON t.user_id = p.user_id AND t.project_name = p.name
             WHERE p.id = ? GROUP BY p.id'
        );
        $row->execute([$id]);
        $proj = $row->fetch();
        $proj['archived']    = (bool)$proj['archived'];
        $proj['total_tasks'] = (int)$proj['total_tasks'];
        $proj['done_tasks']  = (int)$proj['done_tasks'];
        if ($proj['total_tasks'] > 0) {
            $proj['progress'] = (int)round($proj['done_tasks'] / $proj['total_tasks'] * 100);
        }
        $proj['label'] = $proj['archived'] ? 'Archived' : $proj['progress'] . '% Complete';
        jsonOk(['data' => $proj]);

    /* ── DELETE ── */
    case 'DELETE':
        if (!$id) jsonErr('Project ID required.');

        $own = $db->prepare('SELECT id FROM projects WHERE id = ? AND user_id = ?');
        $own->execute([$id, $userId]);
        if (!$own->fetch()) jsonErr('Project not found.', 404);

        $db->prepare('DELETE FROM projects WHERE id = ?')->execute([$id]);
        jsonOk(['message' => 'Project deleted.']);

    default:
        jsonErr('Method not allowed.', 405);
}
