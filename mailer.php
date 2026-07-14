<?php
require_once __DIR__ . '/config.php';

/**
 * إرسال إيميل باستخدام دالة mail() المدمجة في PHP.
 *
 * ملحوظة: على أغلب استضافات cPanel المشتركة دي بتشتغل من غير أي إعداد إضافي.
 * لو محتاج ضمان وصول أعلى (خصوصًا لو السيرفر مش موثوق لدى Gmail/Outlook)،
 * الأفضل تركّب مكتبة PHPMailer وتستخدم SMTP (القيم دي موجودة جاهزة في config.php:
 * SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_PASS, SMTP_SECURE) - راجع backend/README.md.
 */
function send_mail(string $to, string $subject, string $body, bool $isHtml = true): bool
{
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $headers   = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: ' . ($isHtml ? 'text/html' : 'text/plain') . '; charset=UTF-8';
    $headers[] = 'From: ' . mb_encode_mimeheader(MAIL_FROM_NAME) . ' <' . MAIL_FROM . '>';
    $headers[] = 'Reply-To: ' . MAIL_FROM;

    $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

    return @mail($to, $encodedSubject, $body, implode("\r\n", $headers));
}
