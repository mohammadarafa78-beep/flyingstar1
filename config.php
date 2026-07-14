<?php
/**
 * ملف الإعدادات - انسخ الملف ده باسم config.php وعدّل القيم حسب استضافتك.
 * لازم config.php يفضل مستبعد من git (متضاف في .gitignore) عشان بيانات
 * الاتصال الحقيقية متتسربش.
 */

// ===== قاعدة البيانات =====
define('DB_HOST', 'localhost');
define('DB_NAME', 'flyingstar');
define('DB_USER', 'flyingstar_user');
define('DB_PASS', 'CHANGE_ME');
define('DB_CHARSET', 'utf8mb4');

// ===== الإيميل =====
// العنوان اللي هيظهر كمرسل، ورسائل الموقع (زي "تواصل معنا") هتتبعت عليه
define('MAIL_FROM', 'no-reply@example.com');
define('MAIL_FROM_NAME', 'FlyingStar');

// إعدادات SMTP اختيارية (لو سيبتها فاضية، هيستخدم دالة mail() المدمجة في PHP)
// لو محتاج إرسال أضمن (Gmail/Outlook)، ركّب PHPMailer وفعّل دي - راجع backend/README.md
define('SMTP_HOST', '');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_SECURE', 'tls'); // tls أو ssl

// ===== الأمان =====
// غيّر القيمة دي لنص عشوائي طويل وسرّي (تستخدم مستقبلًا لأي تشفير/توكن)
define('APP_SECRET', 'change-this-to-a-long-random-string');

// وضع التطوير: يعرض تفاصيل الأخطاء في الـ log. خليه false في الإنتاج
define('APP_DEBUG', false);
