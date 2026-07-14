<?php
require_once __DIR__ . '/auth.php';

require_method('POST');
start_secure_session();

$input    = json_input();
$phone    = trim((string)($input['phone'] ?? ''));
$password = (string)($input['password'] ?? '');

if ($phone === '' || $password === '') {
    fail('اكتب رقم الهاتف وكلمة السر');
}

$pdo  = db();
$stmt = $pdo->prepare('SELECT id, name, phone, password_hash FROM users WHERE phone = ?');
$stmt->execute([$phone]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    fail('رقم الهاتف أو كلمة السر غلط', 401);
}

session_regenerate_id(true);
$_SESSION['user_id'] = (int)$user['id'];

ok(['user' => ['id' => (int)$user['id'], 'name' => $user['name'], 'phone' => $user['phone']]]);
