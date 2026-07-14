<?php
/**
 * دوال مساعدة عامة لأي endpoint: قراءة JSON، الرد بـJSON، والتحقق من طريقة الطلب.
 */

header('Content-Type: application/json; charset=utf-8');

/** يقرأ جسم الطلب كـJSON ويرجعه كـarray (فاضي لو مفيش بيانات صالحة) */
function json_input(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') return [];
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

/** يرد برسالة JSON ويوقف التنفيذ */
function respond($data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/** رد نجاح موحّد: {"ok": true, ...extra} */
function ok(array $extra = []): void
{
    respond(array_merge(['ok' => true], $extra), 200);
}

/** رد فشل موحّد: {"ok": false, "error": "..."} */
function fail(string $message, int $status = 400): void
{
    respond(['ok' => false, 'error' => $message], $status);
}

/** يتأكد إن طريقة الطلب زي المتوقع، وإلا يرجع خطأ 405 */
function require_method(string $method): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== $method) {
        fail('طريقة الطلب غير مسموحة', 405);
    }
}
