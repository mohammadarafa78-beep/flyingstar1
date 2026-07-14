<?php
require_once __DIR__ . '/auth.php';

$uid = require_login();
$pdo = db();

$method = $_SERVER['REQUEST_METHOD'] ?? '';

if ($method === 'GET') {
    $stmt = $pdo->prepare(
        'SELECT id, trip_name, driver_name, trip_date, cargo_weight, funded,
                fund_extras_json, items_json, total, remain, save_title, saved_at
         FROM trips WHERE user_id = ? ORDER BY saved_at DESC'
    );
    $stmt->execute([$uid]);
    $trips = $stmt->fetchAll();

    foreach ($trips as &$t) {
        $t['fundExtras'] = json_decode($t['fund_extras_json'] ?? '[]', true) ?: [];
        $t['rows']       = json_decode($t['items_json'] ?? '[]', true) ?: [];
        $t['funded']     = (float)$t['funded'];
        $t['total']      = (float)$t['total'];
        $t['remain']     = (float)$t['remain'];
        unset($t['fund_extras_json'], $t['items_json']);
    }
    unset($t);

    ok(['trips' => $trips]);
}

if ($method === 'POST') {
    $input = json_input();

    $stmt = $pdo->prepare(
        'INSERT INTO trips
            (user_id, trip_name, driver_name, trip_date, cargo_weight, funded,
             fund_extras_json, items_json, total, remain, save_title, saved_at, created_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())'
    );
    $stmt->execute([
        $uid,
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
    ]);

    ok(['id' => (int)$pdo->lastInsertId()]);
}

fail('طريقة الطلب غير مسموحة', 405);
