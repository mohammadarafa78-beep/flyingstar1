# FlyingStar Backend (PHP + MySQL)

باك إند عام بلغة PHP جاهز يتوسع لأي غرض: تسجيل مستخدمين، حفظ الرحلات في قاعدة
بيانات حقيقية (بدل `localStorage`)، وإرسال إيميلات. الفرونت إند الحالي (`index.html`,
`reports.html`) **لسه مش متوصل بيه تلقائيًا** — شغال زي ما هو بـ`localStorage` عشان
مفيش حاجة تتبهدل. الربط بينهم خطوة منفصلة اطلبها لو حابب.

## المتطلبات

- استضافة بتدعم PHP 7.4+ وMySQL/MariaDB (أي استضافة cPonel عادية بتدعم ده)
- إنشاء قاعدة بيانات ومستخدم ليها من لوحة تحكم الاستضافة

## التركيب

1. اعمل قاعدة بيانات MySQL من لوحة تحكم الاستضافة (cPanel → MySQL Databases)
2. استورد `database/schema.sql` على القاعدة دي (phpMyAdmin → Import)
3. انسخ `config.sample.php` باسم `config.php` وعدّل فيه:
   - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` ببيانات القاعدة اللي عملتها
   - `MAIL_FROM` بإيميل حقيقي على نفس الدومين (يقلل احتمال اعتبار الإيميل سبام)
   - `APP_SECRET` بنص عشوائي طويل
4. ارفع مجلد `backend/` كامل على الاستضافة (مع `frontend files`/جذر الموقع)
5. تأكد إن `config.php` مش قابل للوصول من المتصفح مباشرة (ملف `.htaccess`
   بيعمل ده تلقائيًا على Apache؛ لو الاستضافة بتستخدم Nginx هتحتاج تضيف قاعدة
   مشابهة في إعدادات السيرفر)

## الـEndpoints المتاحة

كل الردود بصيغة JSON. الطلبات اللي بتغيّر بيانات (تسجيل، إضافة رحلة...) بتتبعت بـ`POST`/`PUT`/`DELETE` وبجسم JSON.

| Endpoint | Method | الوصف | يحتاج تسجيل دخول؟ |
|---|---|---|---|
| `register.php` | POST | تسجيل مستخدم جديد `{name, phone, password}` | لا |
| `login.php` | POST | تسجيل دخول `{phone, password}` | لا |
| `logout.php` | POST | تسجيل خروج | نعم |
| `me.php` | GET | بيانات المستخدم الحالي | نعم |
| `trips.php` | GET | كل رحلات المستخدم الحالي | نعم |
| `trips.php` | POST | حفظ رحلة جديدة | نعم |
| `trip.php?id=ID` | GET | تفاصيل رحلة واحدة | نعم |
| `trip.php?id=ID` | PUT | تعديل رحلة | نعم |
| `trip.php?id=ID` | DELETE | حذف رحلة | نعم |
| `contact.php` | POST | إرسال رسالة/إشعار بالإيميل `{name, email?, message}` | لا |

المصادقة بتتم بجلسة (Session) عن طريق كوكي `httponly` تلقائي، يعني أي طلب
`fetch` من نفس الدومين بعد تسجيل الدخول بيبقى معروف تلقائيًا (لازم تضيف
`credentials: 'include'` في الطلبات من الفرونت إند).

### مثال استدعاء من JavaScript

```js
// تسجيل دخول
await fetch('/backend/login.php', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  credentials: 'include',
  body: JSON.stringify({phone: '0100...', password: '......'})
});

// حفظ رحلة (بعد تسجيل الدخول)
await fetch('/backend/trips.php', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  credentials: 'include',
  body: JSON.stringify(collectCurrentTrip())
});
```

## ملاحظات مهمة

- **الأمان**: كلمات السر متخزنة مشفّرة (`password_hash`)، والاستعلامات كلها
  Prepared Statements (حماية من SQL Injection). لكن ده أساس عام، مش نظام
  مُدقّق أمنيًا بالكامل — لو الموقع هيتعامل مع فلوس حقيقية بكميات كبيرة،
  يستحق مراجعة أمنية إضافية (Rate limiting على تسجيل الدخول، CSRF tokens، إلخ).
- **الإيميل**: بيستخدم دالة `mail()` المدمجة في PHP كحل افتراضي بسيط. لو
  الرسائل بتوصل سبام أو مش بتوصل خالص، ركّب [PHPMailer](https://github.com/PHPMailer/PHPMailer)
  عبر Composer واستخدم إعدادات SMTP الموجودة في `config.php`.
- **ربط الفرونت إند**: لو عايز الموقع فعليًا يستخدم القاعدة دي بدل
  `localStorage` (يعني مزامنة بين الأجهزة + تسجيل دخول)، ده هيحتاج:
  1. شاشة تسجيل دخول/تسجيل جديد في الواجهة
  2. تعديل كائن `store` في `index.html`/`reports.html` عشان ينادي على
     `trips.php`/`trip.php` بدل `localStorage`/`window.storage`
  قوللي لو عايز أنفذ الخطوة دي.
