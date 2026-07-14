<?php
require_once __DIR__ . '/auth.php';

require_method('POST');
start_secure_session();

$input    = json_input();
$name     = trim((string)($input['name'] ?? ''));
$phone    = trim((string)($input['phone'] ?? ''));
$password = (string)($input['password'] ?? '');

if ($name === '' || $phone === '') {
    fail('الاسم ورقم الهاتف مطلوبين');
}
if (mb_strlen($password) < 6) {
    fail('كلمة السر لازم تكون 6 أحرف على الأقل');
}

$pdo = db();

$stmt = $pdo->prepare('SELECT id FROM users WHERE phone = ?');
$stmt->execute([$phone]);
if ($stmt->fetch()) {
    fail('رقم الهاتف ده مسجل قبل كده');
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('INSERT INTO users (name, phone, password_hash, created_at) VALUES (?, ?, ?, NOW())');
$stmt->execute([$name, $phone, $hash]);
$userId = (int)$pdo->lastInsertId();

session_regenerate_id(true);
$_SESSION['user_id'] = $userId;

ok(['user' => ['id' => $userId, 'name' => $name, 'phone' => $phone]]);
