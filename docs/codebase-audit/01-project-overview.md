# ۱. نمای کلی پروژه

**وضعیت بازرسی:** تأییدشده از روی کد منبع  
**تاریخ ممیزی:** ۱۲ اکتبر ۲۰۲۶  
**نکته مهم:** این پروژه **Laravel نیست**. استک واقعی **CodeIgniter 3** است.

---

## ۱. هدف پروژه و ماژول‌های کشف‌شده

**حساب‌یار (expense-manager)** یک سامانه مدیریت درآمد و هزینه شخصی است. کاربر ثبت‌نام می‌کند، دسته‌بندی می‌سازد، تراکنش ثبت می‌کند، موجودی (budget) به‌صورت تجمیعی به‌روز می‌شود، و داشبورد + نمودار وضعیت مالی را می‌بیند.

| ماژول | مسئولیت |
|--------|---------|
| Auth | ثبت‌نام، ورود، خروج، remember-me، بازیابی حساب soft-deleted، حذف حساب |
| Profile | مشاهده/ویرایش پروفایل، آپلود آواتار، تغییر رمز |
| Categories | CRUD + soft-delete + restore دسته‌بندی‌های income/expense |
| Transactions | CRUD + فیلتر/صفحه‌بندی + soft-delete + restore |
| Dashboard | خلاصه موجودی، درآمد/هزینه ۳۰ روز اخیر |
| Chart | سری زمانی درآمد/هزینه/موجودی (۷ یا ۳۰ روز) |
| Migrate / Factory | ابزارهای توسعه (مهاجرت و داده آزمایشی) |

**ویژگی‌های غایب (تأیید عدم وجود):** سبد خرید، پرداخت، سفارش، OTP، پنل ادمین چندکاربره، صف/جاب، اعلان، webhook، چندارزی، نقش/مجوز پیشرفته.

---

## ۲. استک فناوری و نسخه‌ها (تأییدشده)

| لایه | فناوری | منبع شواهد |
|------|--------|------------|
| Backend framework | **CodeIgniter 3.1.13** | `composer.json`, `composer.lock` |
| PHP libs | nesbot/carbon `^3.14` (استفاده در `Factory`) | `composer.json` |
| Database | MySQL/MariaDB (mysqli) | `application/config/database.php` |
| Session | فایل‌محور CI (`sess_driver = files`) | `config.php` |
| Frontend UI | PHP views + **Vue 3** (CDN global) | `navbar.php` |
| HTTP client | Axios (CDN) | `navbar.php` |
| CSS | Bootstrap 5.3.3 RTL + CSS سفارشی | `navbar.php`, `public/assets/css/` |
| Charts | ApexCharts (CDN/local assets) | `line_chart.php`, `package.json` |
| Dates | persian-date + persian-datepicker + jQuery | `ta_index.php` |
| Build | تقریباً بدون باندل؛ `prettier` فقط | `package.json` |

ورود وب: `public/index.php` → فریم‌ورک CI در `system/` + اپ در `application/`.

---

## ۳. معماری سطح بالا

الگوی واقعی پروژه یک **لایه سرویس سبک روی CodeIgniter** است (نه معماری تمیز کامل Laravel):

```
Browser (Vue islands در PHP views)
    │  Axios + cookie session
    ▼
public/index.php  →  CodeIgniter Router (routes.php)
    ▼
Page Controllers (Home, Auth)  |  API Controllers (*_API_Controller)
    ▼
Validators (اختیاری)  →  Services  →  Policies (مالکیت)  →  Models (Query Builder)
    ▼
MySQL
```

- **SSR سبک:** صفحات `login` / `signup` / `home` / `profile` با PHP view رندر می‌شوند.
- **جزیره‌های Vue:** هر صفحه چند `Vue.createApp` جدا برای فرم‌ها و لیست‌ها دارد (نه SPA واحد).
- **API JSON هم‌منشأ:** همان دامنه؛ احراز هویت با session cookie (`ci_session`) + اختیاری `remember_me`.
- **DI دستی:** سرویس‌ها با `new Service(...)` در سازنده کنترلر ساخته می‌شوند (نه container).

---

## ۴. نقشه دایرکتوری بک‌اند

| مسیر | نقش |
|------|-----|
| `application/controllers/` | کنترلرهای صفحه (`Home`, `Auth`) و ابزار (`Migrate`, `Factory`) |
| `application/controllers/api/` | API JSON + `Base_API_Controller` |
| `application/services/` | منطق کسب‌وکار (`Auth`, `Profile`, `Category`, `Transaction`) |
| `application/models/` | دسترسی DB با Query Builder |
| `application/policies/` | بررسی مالکیت/اجازه (فراخوانی دستی از سرویس) |
| `application/validators/` | قوانین `form_validation` برای Auth و Transaction |
| `application/migrations/` | اسکیما sequential شماره ۱ تا ۵ |
| `application/views/` | قالب‌های PHP + اسکریپت Vue درون‌خطی |
| `application/config/` | routes، session، CSRF، DB، migration |
| `system/` | هسته CodeIgniter (کد شخص ثالث فریم‌ورک) |
| `vendor/` | Composer (CI + Carbon) |
| `uploads/avatars/` | فایل آواتار کاربران |
| `public/` | Document root؛ symlink به `uploads` |

---

## ۵. نقشه دایرکتوری فرانت‌اند

| مسیر | نقش |
|------|-----|
| `application/views/home.php` | داشبورد (شامل summary + تراکنش‌ها) |
| `application/views/login.php` / `signup.php` / `profile.php` | صفحات احراز هویت و پروفایل |
| `application/views/auth/*` | کارت‌های ورود/ثبت‌نام/بازیابی |
| `application/views/transactions/*` | لیست، فیلتر، مودال‌ها، Vue app تراکنش |
| `application/views/categories/category_manager.php` | مودال مدیریت دسته |
| `application/views/dashboard/*` | کارت‌های خلاصه + نمودار |
| `application/views/components/*` | navbar، footer، پس‌زمینه |
| `public/assets/js/format.js` | فرمت عدد/تاریخ شمسی برای UI |
| `public/assets/css/*` | استایل اپ |
| `public/assets/js/apexcharts/*` | دارایی‌های نمودار (عمدتاً از npm کپی‌شده) |

**ورود فرانت:** هر صفحه از `components/navbar.php` شروع می‌شود (HTML head + Vue + Axios + Bootstrap).

---

## ۶. نقاط ورود اپلیکیشن

| نقطه ورود | مسیر | توضیح |
|-----------|------|--------|
| HTTP | `public/index.php` | Front controller استاندارد CI3 |
| صفحه پیش‌فرض | `Home::index` via `$route['default_controller'] = 'home'` | نیاز به session |
| مهاجرت | `Migrate::latest` / `Migrate::rollback` | بدون auth در کد |
| Seed آزمایشی | `Factory::transactions` | نیاز به session user_id |

---

## ۷. فایل‌های پیکربندی مهم

| فایل | اهمیت |
|------|--------|
| `application/config/routes.php` | تمام routeهای صریح صفحه و API |
| `application/config/config.php` | `base_url`, session, **CSRF=FALSE**, cookies |
| `application/config/database.php` | اتصال DB (حاوی credential محلی — در docs افشا نمی‌شود) |
| `application/config/autoload.php` | autoload: database, session, form_validation, url helper |
| `application/config/migration.php` | `migration_enabled=TRUE`, version=5 |
| `composer.json` / `package.json` | وابستگی‌ها |

---

## ۸. نمای کلی دیتابیس

جداول مالک اپ (از migrations + تأیید نسبی با `.db-backups/*.sql`):

1. `users` — حساب کاربر + soft delete  
2. `categories` — دسته per-user + soft delete  
3. `budgets` — یک ردیف موجودی per-user  
4. `transactions` — تراکنش + soft delete  
5. `remember_tokens` — توکن remember-me  
6. `migrations` — وضعیت مهاجرت CI  

جزئیات: `06-database-and-data-lifecycle.md`

---

## ۹. یکپارچه‌سازی خارجی

| مورد | وضعیت |
|------|--------|
| پرداخت / درگاه | وجود ندارد |
| ایمیل / SMS | وجود ندارد |
| OAuth | وجود ندارد |
| CDNهای شخص ثالث | Google Fonts, Bootstrap, Vue, Axios, jQuery, persian-date از CDN |
| Postman | پوشه‌های `.postman/` و `postman/` با YAML حداقلی — مجموعه تست کامل کشف نشد |

---

## ۱۰. زیر و بیلد

| مورد | وضعیت تأییدشده |
|------|----------------|
| PHPUnit / feature tests | **وجود ندارد** در کد اپ |
| CI pipeline در ریپو | مشاهده نشد |
| npm scripts | فقط prettier به‌عنوان وابستگی |
| تست دستی | احتمالاً از طریق UI و Postman |

---

## ۱۱. محدوده بازرسی و محدودیت‌ها

**بازرسی‌شده:** controllers، services، models، policies، validators، migrations، routes، configهای کلیدی، views اصلی که API صدا می‌زنند، `format.js`.

**خارج از بازرسی عمیق:** کل `system/` و `vendor/` و هزاران فایل apexcharts، محتوای لاگ‌های runtime، رفتار واقعی سرور production.

**محدودیت runtime:** تست خودکار اجرا نشد (suite وجود ندارد). رفتار CSRF/session در مرورگر به‌صورت استاتیک از config استنباط شد. اختلاف احتمالی بین migration فعلی و dump قدیمی DB در سند دیتابیس ذکر شده است.
