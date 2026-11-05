# نداء كهرباء غزة

موقع نداء كهرباء غزة ولوحة التحكم الخاصة به (Laravel 13، عربي وإنجليزي).

جميع الحقوق محفوظة © شركة كهرباء غزة.

## المتطلبات

- PHP 8.5
- Composer
- Node.js 20 أو أحدث
- MySQL أو MariaDB (مثلًا من XAMPP)

ضع المشروع **خارج** سطح المكتب ومجلدات OneDrive (مثل `C:\dev`)، فالمزامنة تجعل الموقع بطيئًا جدًا.

## التثبيت

```bash
git clone https://github.com/fahim-almamluk/gazaappeal.git
cd gazaappeal
composer install
npm install
copy .env.example .env
```

ملف `.env.example` مضبوط للإنتاج، فعدّل هذه القيم في `.env` للتشغيل المحلي:

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
LOG_LEVEL=debug

DB_USERNAME=root
DB_PASSWORD=

SESSION_SECURE_COOKIE=false
SESSION_ENCRYPT=false

ADMIN_NAME=Admin
ADMIN_EMAIL=
ADMIN_PASSWORD=
```

كلمة مرور المدير 12 حرفًا على الأقل، فيها حروف كبيرة وصغيرة وأرقام ورموز. لا تشاركها في أي مكان غير `.env`.

أنشئ قاعدة بيانات باسم `gazaappeal` (من phpMyAdmin مثلًا)، ثم افتح الطرفية **كمسؤول** (Run as administrator) ونفّذ:

```bash
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan db:seed --class=DemoContentSeeder
php artisan storage:link
```

`DemoContentSeeder` يضيف محتوى تجريبيًا بصور، ولا يُستعمل على الخادم الحقيقي.

## التشغيل

في نافذتين منفصلتين:

```bash
php artisan serve
```

```bash
npm run dev
```

- الموقع: `http://127.0.0.1:8000`
- لوحة التحكم: `http://127.0.0.1:8000/login` ثم `/admin` ببيانات `ADMIN_EMAIL` و`ADMIN_PASSWORD`.

أبقِ `npm run dev` يعمل أثناء التطوير؛ الصفحة تتحدّث تلقائيًا عند تعديل الواجهة.

## الاختبارات

```bash
php artisan test --compact
```

## حل المشاكل الشائعة

| المشكلة | الحل |
|---|---|
| `Vite manifest not found` | شغّل `npm run dev`، أو `npm run build` مرة واحدة |
| الصفحة تظهر بلا تنسيق | أعد تشغيل `npm run dev`، واحذف الملف `public\hot` إن بقي بعد إيقافه |
| الصور ترجع 403 أو لا تظهر | `cmd /c rmdir public\storage` ثم `php artisan storage:link` من طرفية كمسؤول |
| خطأ 500 | `php artisan optimize:clear` وراجع `storage\logs\laravel.log` |
| الصفحات بطيئة جدًا | انقل المشروع خارج سطح المكتب وOneDrive، واستثنِه من فحص Windows Defender |

> لا تحذف `public\storage` بأمر `rmdir` أو `Remove-Item` في PowerShell؛ قد يحذف الصور الأصلية داخل `storage\app\public`. استعمل `cmd /c rmdir` فقط.

## النشر

قبل الرفع على الخادم: `APP_ENV=production` و`APP_DEBUG=false` و`APP_URL` الحقيقي، ومستخدم قاعدة بيانات بكلمة مرور، ثم:

```bash
composer install --no-dev --optimize-autoloader
npm run build
php artisan migrate --force
php artisan optimize
```

فعّل ضغط gzip أو brotli على الخادم.

توثيق البنية والقرارات في [`docs/PROJECT.md`](docs/PROJECT.md).
