<?php
require_once __DIR__ . '/config.php';

/**
 * يرجع اتصال PDO واحد مشترك (Singleton) بقاعدة البيانات.
 */
function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                error_log('DB connection failed: ' . $e->getMessage());
            }
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => false, 'error' => 'تعذر الاتصال بقاعدة البيانات، راجع إعدادات config.php']);
            exit;
        }
    }
    return $pdo;
}
