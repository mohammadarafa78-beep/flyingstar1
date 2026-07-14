<?php
require_once __DIR__ . '/auth.php';

$uid = require_login();
$pdo = db();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    fail('رقم الرحلة مطلوب');
}

// نتأكد إن الرحلة دي فعلًا بتاعة المستخدم المسجل دخول (منع الوصول لبيانات غيره)
$stmt = $pdo->prepare('SELECT user_id FROM trips WHERE id = ?');
$stmt->execute([$id]);
$owner = $stmt->fetch();
if (!$owner || (int)$owner['user_id'] !== $uid) {
    fail('الرحلة مش موجودة', 404);
}

$method = $_SERVER['REQUEST_METHOD'] ?? '';

if ($method === 'GET') {
    $stmt = $pdo->prepare('SELECT * FROM trips WHERE id = ?');
    $stmt->execute([$id]);
    $trip = $stmt->fetch();
    $trip['fundExtras'] = json_decode($trip['fund_extras_json'] ?? '[]', true) ?: [];
    $trip['rows']       = json_decode($trip['items_json'] ?? '[]', true) ?: [];
    unset($trip['fund_extras_json'], $trip['items_json']);
    ok(['trip' => $trip]);
}

if ($method === 'PUT') {
    $input = json_input();
    $stmt = $pdo->prepare(
        'UPDATE trips SET
            trip_name = ?, driver_name = ?, trip_date = ?, cargo_weight = ?,
            funded = ?, fund_extras_json = ?, items_json = ?, total = ?, remain = ?, save_title = ?
         WHERE id = ?'
    );
    $stmt->execute([
        trim((string)($input['tripName'] ?? '')),
        trim((string)($input['driverName'] ?? '')),
        $input['tripDateISO'] ?? null,
        trim((string)($input['cargoWeight'] ?? '')),
        (float)($input['funded'] ?? 0),
        json_encode($input['fundExtras'] ?? [], JSON_UNESCAPED_UNICODE),
        json_encode($input['rows'] ?? [], JSON_UNESCAPED_UNICODE),
        (float)($input['total'] ?? 0),
        (float)($input['remain'] ?? 0),
        trim((string)($input['saveTitle'] ?? '')),
        $id,
    ]);
    ok();
}

if ($method === 'DELETE') {
    $stmt = $pdo->prepare('DELETE FROM trips WHERE id = ?');
    $stmt->execute([$id]);
    ok();
}

fail('طريقة الطلب غير مسموحة', 405);
