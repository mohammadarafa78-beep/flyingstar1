<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

/** يبدأ جلسة آمنة (كوكي httponly، samesite=Lax، secure لو HTTPS) */
function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => !empty($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

/** يرجع ID المستخدم الحالي لو مسجل دخول، أو null */
function current_user_id(): ?int
{
    start_secure_session();
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

/** يتأكد إن المستخدم مسجل دخول، وإلا يرجع خطأ 401 ويوقف التنفيذ */
function require_login(): int
{
    $uid = current_user_id();
    if (!$uid) {
        fail('لازم تسجل الدخول الأول', 401);
    }
    return $uid;
}
