<?php
/**
 * DayTrack – Database Auto-Setup
 * Akses: http://localhost/apptodolist/setup.php
 *
 * Script ini akan membuat database dan tabel secara otomatis.
 * HAPUS file ini setelah setup selesai!
 */

$host    = 'localhost';
$user    = 'root';
$pass    = '';          // Ganti jika MySQL Anda punya password
$dbname  = 'daytrack';
$charset = 'utf8mb4';

$steps   = [];
$errors  = [];

/* ── Connect tanpa database dulu ── */
try {
    $pdo = new PDO("mysql:host={$host};charset={$charset}", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $steps[] = ['ok', 'Koneksi MySQL berhasil ✅ (user: root)'];
} catch (PDOException $e) {
    $errors[] = 'Gagal koneksi MySQL: ' . $e->getMessage();
}

if (empty($errors)) {
    /* ── Create Database ── */
    try {
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `{$dbname}`");
        $steps[] = ['ok', "Database `{$dbname}` siap ✅"];
    } catch (PDOException $e) {
        $errors[] = 'Gagal buat database: ' . $e->getMessage();
    }
}

if (empty($errors)) {
    /* ── Create Tables ── */
    $tables = [
        'users' => "CREATE TABLE IF NOT EXISTS `users` (
            `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
            `name`       VARCHAR(120)  NOT NULL,
            `email`      VARCHAR(180)  NOT NULL UNIQUE,
            `password`   VARCHAR(255)  NOT NULL,
            `role`       VARCHAR(60)   NOT NULL DEFAULT 'Team Member',
            `bio`        TEXT                   DEFAULT NULL,
            `avatar`     VARCHAR(255)           DEFAULT NULL,
            `created_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        'projects' => "CREATE TABLE IF NOT EXISTS `projects` (
            `id`          INT UNSIGNED      NOT NULL AUTO_INCREMENT,
            `user_id`     INT UNSIGNED      NOT NULL,
            `name`        VARCHAR(120)      NOT NULL,
            `color`       VARCHAR(30)       NOT NULL DEFAULT 'primary',
            `icon`        VARCHAR(60)       NOT NULL DEFAULT 'bi-briefcase',
            `description` TEXT                      DEFAULT NULL,
            `members`     SMALLINT UNSIGNED NOT NULL DEFAULT 1,
            `progress`    TINYINT UNSIGNED  NOT NULL DEFAULT 0,
            `archived`    TINYINT(1)        NOT NULL DEFAULT 0,
            `created_at`  DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `fk_proj_user` (`user_id`),
            CONSTRAINT `fk_proj_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        'tasks' => "CREATE TABLE IF NOT EXISTS `tasks` (
            `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id`      INT UNSIGNED NOT NULL,
            `project_name` VARCHAR(120) NOT NULL DEFAULT 'General',
            `title`        VARCHAR(255) NOT NULL,
            `done`         TINYINT(1)   NOT NULL DEFAULT 0,
            `priority`     ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
            `due_date`     DATE                 DEFAULT NULL,
            `notes`        TEXT                 DEFAULT NULL,
            `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `fk_task_user` (`user_id`),
            CONSTRAINT `fk_task_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        'meetings' => "CREATE TABLE IF NOT EXISTS `meetings` (
            `id`          INT UNSIGNED      NOT NULL AUTO_INCREMENT,
            `user_id`     INT UNSIGNED      NOT NULL,
            `title`       VARCHAR(180)      NOT NULL,
            `meet_time`   TIME              NOT NULL DEFAULT '09:00:00',
            `duration`    SMALLINT UNSIGNED NOT NULL DEFAULT 30,
            `members`     SMALLINT UNSIGNED NOT NULL DEFAULT 2,
            `type`        VARCHAR(30)       NOT NULL DEFAULT 'standup',
            `link`        VARCHAR(500)              DEFAULT '#',
            `notes`       TEXT                      DEFAULT NULL,
            `created_at`  DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `fk_meet_user` (`user_id`),
            CONSTRAINT `fk_meet_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        'messages' => "CREATE TABLE IF NOT EXISTS `messages` (
            `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id`      INT UNSIGNED NOT NULL,
            `sender_name`  VARCHAR(120) NOT NULL,
            `body`         TEXT         NOT NULL,
            `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `fk_msg_user` (`user_id`),
            CONSTRAINT `fk_msg_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    ];

    foreach ($tables as $name => $sql) {
        try {
            $pdo->exec($sql);
            $steps[] = ['ok', "Tabel `{$name}` dibuat / sudah ada ✅"];
        } catch (PDOException $e) {
            $errors[] = "Gagal buat tabel `{$name}`: " . $e->getMessage();
        }
    }
}

if (empty($errors)) {
    /* ── Seed demo users ── */
    $check = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($check == 0) {
        // password = password123
        $hash = password_hash('password123', PASSWORD_BCRYPT, ['cost' => 10]);
        $pdo->prepare("INSERT INTO users (name, email, password, role, bio) VALUES (?, ?, ?, ?, ?)")->execute([
            'Rian Pratama', 'rian@demo.com', $hash, 'Team Lead', 'Full-stack developer and project lead.'
        ]);
        $userId = (int)$pdo->lastInsertId();

        /* Seed projects */
        $projects = [
            [$userId, 'Design System',  'warning', 'bi-layers',        'UI component library and design tokens.',   5, 80, 0],
            [$userId, 'Marketing Web',  'danger',  'bi-globe',          'Company marketing site redesign.',          3, 25, 0],
            [$userId, 'User Analytics', 'primary', 'bi-bar-chart-line', 'Analytics dashboard (archived).',          2, 100,1],
        ];
        $ps = $pdo->prepare("INSERT INTO projects (user_id,name,color,icon,description,members,progress,archived) VALUES (?,?,?,?,?,?,?,?)");
        foreach ($projects as $p) $ps->execute($p);

        /* Seed tasks */
        $tasks = [
            [$userId, 'Marketing Web',  'Refactor Landing Page Hero',  0,'high'],
            [$userId, 'Management',     'Sprint Planning Document',     0,'medium'],
            [$userId, 'Design System',  'Team Avatar Review',          1,'low'],
            [$userId, 'General',        'Write API Documentation',     0,'medium'],
            [$userId, 'Design System',  'Update onboarding flow',      0,'high'],
        ];
        $ts = $pdo->prepare("INSERT INTO tasks (user_id,project_name,title,done,priority) VALUES (?,?,?,?,?)");
        foreach ($tasks as $t) $ts->execute($t);

        /* Seed meetings */
        $meetings = [
            [$userId,'Weekly Sprint Sync','09:00',30,6,'standup','#','Review sprint goals.'],
            [$userId,'Design Review',     '11:30',45,4,'review', '#','Check Figma files.'],
            [$userId,'Stakeholder Update','14:00',60,8,'update', '#','Q3 progress update.'],
            [$userId,'1-on-1 with Manager','16:30',30,2,'one-on-one','#','Career growth discussion.'],
        ];
        $ms = $pdo->prepare("INSERT INTO meetings (user_id,title,meet_time,duration,members,type,link,notes) VALUES (?,?,?,?,?,?,?,?)");
        foreach ($meetings as $m) $ms->execute($m);

        $steps[] = ['ok', 'Demo data berhasil ditambahkan ✅ (rian@demo.com / password123)'];
    } else {
        $steps[] = ['info', "Data sudah ada — {$check} user terdaftar, skip seed."];
    }
}

/* ── Count data ── */
$stats = [];
if (empty($errors) && isset($pdo)) {
    foreach (['users','tasks','projects','meetings'] as $t) {
        try {
            $stats[$t] = $pdo->query("SELECT COUNT(*) FROM `{$t}`")->fetchColumn();
        } catch (Exception $e) {
            $stats[$t] = '?';
        }
    }
}

$success = empty($errors);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>DayTrack – Setup</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"/>
</head>
<body class="bg-primary d-flex justify-content-center align-items-center min-vh-100 py-4">
<div class="col-12 col-sm-9 col-md-6 col-lg-5 mx-auto" style="max-width:520px;">

  <!-- Brand -->
  <div class="text-center mb-4">
    <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-4 mb-3 shadow" style="width:72px;height:72px;">
      <i class="bi bi-database-check text-primary fs-2"></i>
    </div>
    <h1 class="fw-bold text-white fs-2 mb-1">DayTrack Setup</h1>
    <p class="text-white-50" style="font-size:.85rem;">Database Auto-Installer</p>
  </div>

  <!-- Result Card -->
  <div class="bg-light rounded-4 shadow-lg p-4 mb-3">
    <?php if ($success): ?>
      <div class="d-flex align-items-center gap-3 mb-3">
        <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle rounded-circle flex-shrink-0" style="width:48px;height:48px;">
          <i class="bi bi-check-circle-fill text-success fs-4"></i>
        </div>
        <div>
          <h2 class="fw-bold text-dark fs-5 mb-0">Setup Berhasil! 🎉</h2>
          <p class="text-muted mb-0" style="font-size:.8rem;">Database dan tabel siap digunakan</p>
        </div>
      </div>
    <?php else: ?>
      <div class="d-flex align-items-center gap-3 mb-3">
        <div class="d-inline-flex align-items-center justify-content-center bg-danger-subtle rounded-circle flex-shrink-0" style="width:48px;height:48px;">
          <i class="bi bi-exclamation-triangle-fill text-danger fs-4"></i>
        </div>
        <div>
          <h2 class="fw-bold text-dark fs-5 mb-0">Setup Gagal ❌</h2>
          <p class="text-muted mb-0" style="font-size:.8rem;">Ada error, cek detail di bawah</p>
        </div>
      </div>
    <?php endif; ?>

    <!-- Steps -->
    <div class="d-flex flex-column gap-2 mb-3">
      <?php foreach ($steps as [$type, $msg]): ?>
        <div class="d-flex align-items-center gap-2 p-2 rounded-3 <?= $type === 'ok' ? 'bg-success-subtle' : 'bg-info-subtle' ?>">
          <i class="bi <?= $type === 'ok' ? 'bi-check-circle-fill text-success' : 'bi-info-circle-fill text-info' ?> flex-shrink-0"></i>
          <span style="font-size:.82rem;"><?= htmlspecialchars($msg) ?></span>
        </div>
      <?php endforeach; ?>
      <?php foreach ($errors as $err): ?>
        <div class="d-flex align-items-center gap-2 p-2 rounded-3 bg-danger-subtle">
          <i class="bi bi-x-circle-fill text-danger flex-shrink-0"></i>
          <span style="font-size:.82rem;"><?= htmlspecialchars($err) ?></span>
        </div>
      <?php endforeach; ?>
    </div>

    <?php if (!empty($stats)): ?>
    <!-- Stats -->
    <div class="row row-cols-4 g-2 mb-4">
      <?php
      $icons = ['users'=>'bi-people','tasks'=>'bi-check2-square','projects'=>'bi-kanban','meetings'=>'bi-camera-video'];
      $colors = ['users'=>'primary','tasks'=>'success','projects'=>'warning','meetings'=>'info'];
      foreach ($stats as $t => $c):
      ?>
      <div class="col">
        <div class="card border-0 rounded-3 p-2 text-center bg-<?= $colors[$t] ?>-subtle">
          <i class="bi <?= $icons[$t] ?> text-<?= $colors[$t] ?> mb-1"></i>
          <p class="fw-bold mb-0 text-<?= $colors[$t] ?>"><?= $c ?></p>
          <p class="mb-0 text-muted" style="font-size:.65rem;"><?= ucfirst($t) ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($success): ?>
    <!-- Login credentials -->
    <div class="alert alert-info rounded-3 py-2 mb-4 d-flex align-items-center gap-2" style="font-size:.8rem;">
      <i class="bi bi-info-circle-fill text-info flex-shrink-0"></i>
      <div>Demo login: <strong>rian@demo.com</strong> / <strong>password123</strong></div>
    </div>

    <div class="d-flex gap-2">
      <a href="frontend/pages/login.php" class="btn btn-primary flex-grow-1 fw-bold rounded-3">
        <i class="bi bi-box-arrow-in-right me-1"></i> Buka Login
      </a>
      <a href="api/test.php" class="btn btn-outline-secondary rounded-3" target="_blank">
        <i class="bi bi-bug me-1"></i> Test API
      </a>
    </div>
    <?php else: ?>
    <!-- If DB exists, maybe just wrong password -->
    <div class="alert alert-warning rounded-3 py-2 mb-3" style="font-size:.79rem;">
      <i class="bi bi-exclamation-triangle-fill me-1"></i>
      Jika MySQL Anda punya password, edit baris <code>$pass = ''</code> di <strong>setup.php</strong> dan <strong>config/database.php</strong>
    </div>
    <button onclick="location.reload()" class="btn btn-warning w-100 rounded-3 fw-bold">
      <i class="bi bi-arrow-clockwise me-1"></i> Coba Lagi
    </button>
    <?php endif; ?>
  </div>

  <!-- PHP Info -->
  <div class="text-center text-white-50 mb-2" style="font-size:.75rem;">
    PHP <?= PHP_VERSION ?> &nbsp;·&nbsp; <?= php_uname('s') ?>
  </div>
</div>
</body>
</html>
