# ۱۳. پوشش بازرسی

## دایرکتوری‌ها و فایل‌های کشف‌شده (اپ)

| مسیر | وضعیت بازرسی |
|------|----------------|
| `application/config/routes.php` | کامل خوانده شد |
| `application/config/config.php` | کلیدهای امنیتی/session/CSRF بررسی شد |
| `application/config/autoload.php` | کامل |
| `application/config/migration.php` | کامل |
| `application/config/database.php` | ساختار بررسی شد؛ secret در docs نیامد |
| `application/controllers/*` | همه کنترلرهای اپ خوانده شد |
| `application/controllers/api/*` | همه خوانده شد |
| `application/services/*` | هر ۴ سرویس کامل |
| `application/models/*` | هر ۵ مدل کامل |
| `application/policies/*` | هر ۴ policy کامل |
| `application/validators/*` | هر ۲ validator کامل |
| `application/migrations/001–005` | کامل |
| `application/helpers/helper_helper.php` | کامل |
| `application/hooks` | خالی از منطق |
| `application/libraries` | خالی از منطق اپ |
| `application/core` | خالی |
| Views اصلی auth/home/profile/transactions/categories/dashboard/components | خوانده شد |
| `public/index.php` | سرآغاز بررسی شد |
| `public/assets/js/format.js` | کامل |
| `composer.json` / `package.json` / lock | بررسی شد |
| `.db-backups/*.sql` | فقط schema patterns؛ داده حساس در docs نیست |
| `system/` / `vendor/` | خارج از بازرسی عمیق (فریم‌ورک) |
| `public/assets/js/apexcharts/**` | شمارش به‌عنوان دارایی شخص ثالث؛ منطق اپ نیست |
| `node_modules/` | رد شد |

## Routeها

| دسته | تعداد تقریبی | پوشش سند |
|------|--------------|----------|
| صفحات در routes.php | ۵ | جدول + مرجع |
| API در routes.php | ۲۲ | جدول + مرجع + جریان‌ها |
| ابزار ضمنی Migrate/Factory | ۴+ | شناسایی + ریسک |

همه routeهای اپ در `02` و `04` پوشش داده شده‌اند.

## کنترلرها و اکشن‌ها

همه متدهای عمومی کنترلرهای اپ ردیابی شدند.

## مدل‌ها و migrations

۵ migration و ۵ model بازرسی شدند.

## درخواست‌های فرانت نگاشت‌شده

همه `site_url('api/...')` در views اپ در `07` نگاشت شدند.

## تست‌ها

Suite وجود نداشت؛ اجرایی انجام نشد.

## موارد خارج از بازرسی جزئی

- `application/views/errors/**` قالب‌های پیش‌فرض CI
- `application/views/vue.test.php` سطحی (صفحه آزمایشی)
- `application/language/**`
- پیکربندی وب‌سرور خارج ریپو (nginx/apache واقعی)
- رفتار runtime DB زنده در لحظه ممیزی

## ارجاعات حل‌نشده / مبهم

1. آیا DB زنده دقیقاً با migration 004 (CASCADE) هم‌خوان است یا با dump قدیمی (SET NULL)؟  
2. آیا ستون `full_name_normalized` در DB فعلی وجود دارد؟  
3. آیا `sess_save_path` NULL در محیط فعلی writable است؟  
4. آیا document root واقعاً `public/` است در همه محیط‌ها؟

## کار باقی‌مانده پیشنهادی

- راستی‌آزمایی runtime با `SHOW CREATE TABLE`  
- افزودن تست خودکار برای بودجه و authz  
- بازبینی postman collection کامل در صورت وجود خارج ریپو  
