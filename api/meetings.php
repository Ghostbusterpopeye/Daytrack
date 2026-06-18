<?php
/**
 * DayTrack – Meetings API
 * api/meetings.php
 *
 * GET              → list all meetings for logged-in user
 * POST             → create new meeting
 * PUT  ?id=X       → update meeting
 * DELETE ?id=X     → delete meeting
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
            'SELECT id, title,
                    TIME_FORMAT(meet_time, "%H:%i") AS `time`,
                    duration, members, type, link, notes, created_at
             FROM meetings WHERE user_id = ? ORDER BY meet_time ASC'
        );
        $stmt->execute([$userId]);
        jsonOk(['data' => $stmt->fetchAll()]);

    /* ── CREATE ── */
    case 'POST':
        $title    = trim($data['title']    ?? '');
        $time     = $data['time']          ?? '09:00';
        $duration = max(5, (int)($data['duration'] ?? 30));
        $members  = max(1, (int)($data['members']  ?? 2));
        $type     = trim($data['type']     ?? 'standup');
        $link     = trim($data['link']     ?? '#');
        $notes    = trim($data['notes']    ?? '');

        if (!$title) jsonErr('Meeting title is required.');

        $stmt = $db->prepare(
            'INSERT INTO meetings (user_id, title, meet_time, duration, members, type, link, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$userId, $title, $time, $duration, $members, $type, $link, $notes]);
        $newId = $db->lastInsertId();

        $row = $db->prepare(
            'SELECT id, title, TIME_FORMAT(meet_time,"%H:%i") AS `time`,
                    duration, members, type, link, notes, created_at
             FROM meetings WHERE id = ?'
        );
        $row->execute([$newId]);
        jsonOk(['data' => $row->fetch()]);

    /* ── UPDATE ── */
    case 'PUT':
        if (!$id) jsonErr('Meeting ID required.');

        $own = $db->prepare('SELECT id FROM meetings WHERE id = ? AND user_id = ?');
        $own->execute([$id, $userId]);
        if (!$own->fetch()) jsonErr('Meeting not found.', 404);

        $fields = [];
        $params = [];

        if (isset($data['title']))    { $fields[] = 'title = ?';     $params[] = trim($data['title']); }
        if (isset($data['time']))     { $fields[] = 'meet_time = ?'; $params[] = $data['time']; }
        if (isset($data['duration'])) { $fields[] = 'duration = ?';  $params[] = (int)$data['duration']; }
        if (isset($data['members']))  { $fields[] = 'members = ?';   $params[] = (int)$data['members']; }
        if (isset($data['type']))     { $fields[] = 'type = ?';      $params[] = $data['type']; }
        if (isset($data['link']))     { $fields[] = 'link = ?';      $params[] = $data['link']; }
        if (isset($data['notes']))    { $fields[] = 'notes = ?';     $params[] = trim($data['notes']); }

        if (!$fields) jsonErr('Nothing to update.');

        $params[] = $id;
        $db->prepare('UPDATE meetings SET ' . implode(', ', $fields) . ' WHERE id = ?')
           ->execute($params);

        $row = $db->prepare(
            'SELECT id, title, TIME_FORMAT(meet_time,"%H:%i") AS `time`,
                    duration, members, type, link, notes, created_at
             FROM meetings WHERE id = ?'
        );
        $row->execute([$id]);
        jsonOk(['data' => $row->fetch()]);

    /* ── DELETE ── */
    case 'DELETE':
        if (!$id) jsonErr('Meeting ID required.');

        $own = $db->prepare('SELECT id FROM meetings WHERE id = ? AND user_id = ?');
        $own->execute([$id, $userId]);
        if (!$own->fetch()) jsonErr('Meeting not found.', 404);

        $db->prepare('DELETE FROM meetings WHERE id = ?')->execute([$id]);
        jsonOk(['message' => 'Meeting deleted.']);

    default:
        jsonErr('Method not allowed.', 405);
}
