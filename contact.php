<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/mailer.php';

require_method('POST');

$input   = json_input();
$name    = trim((string)($input['name'] ?? ''));
$email   = trim((string)($input['email'] ?? ''));
$message = trim((string)($input['message'] ?? ''));

if ($name === '' || $message === '') {
    fail('الاسم والرسالة مطلوبين');
}

$safeName    = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$safeEmail   = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
$safeMessage = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));

$body  = "<p><strong>رسالة جديدة من تطبيق FlyingStar</strong></p>";
$body .= "<p>الاسم: {$safeName}</p>";
if ($email !== '') {
    $body .= "<p>الإيميل: {$safeEmail}</p>";
}
$body .= "<p>الرسالة:<br>{$safeMessage}</p>";

$sent = send_mail(MAIL_FROM, 'رسالة جديدة - FlyingStar', $body);

if ($sent) {
    ok();
} else {
    fail('تعذر إرسال الرسالة، حاول لاحقًا', 500);
}
