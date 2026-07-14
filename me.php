<?php
require_once __DIR__ . '/auth.php';

require_method('GET');
$uid = require_login();

$pdo  = db();
$stmt = $pdo->prepare('SELECT id, name, phone, created_at FROM users WHERE id = ?');
$stmt->execute([$uid]);
$user = $stmt->fetch();

if (!$user) {
    fail('المستخدم مش موجود', 404);
}

ok(['user' => $user]);
