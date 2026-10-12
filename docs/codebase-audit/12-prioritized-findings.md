# ۱۲. یافته‌ها و توصیه‌های اولویت‌بندی‌شده

یافته‌ها توصیه‌ای هستند؛ در این ممیزی کدی تغییر داده نشده است.

---

## Critical

| ID | موضوع | شواهد | تأثیر | اقدام پیشنهادی | اولویت |
|----|--------|--------|--------|-----------------|--------|
| F-CRIT-01 | Migrate وب بدون auth | `Migrate.php`, `migration_enabled=TRUE` | تخریب اسکیما | بستن از وب / CLI-only / auth | P0 |
| F-CRIT-02 | Secrets/backup کاربر در درخت پروژه | `database.php`, `.db-backups/*.sql` | نشت credential و PII | env، حذف backup از git، rotate | P0 |

## Security High

| ID | موضوع | شواهد | اقدام |
|----|--------|--------|--------|
| F-SEC-01 | CSRF خاموش | `config.php` | فعال‌سازی CSRF یا توکن API |
| F-SEC-02 | Rate limit غایب روی auth | `Auth_API_Controller` | throttle |
| F-SEC-03 | Enumeration حساب حذف‌شده | پاسخ `account_deleted` | پیام یکسان + جریان جدا امن |

## Data / Financial

| ID | Severity | موضوع | اقدام |
|----|----------|--------|--------|
| F-DATA-01 | High | بودجه denormalized بدون قفل | `SELECT FOR UPDATE` روی budgets |
| F-DATA-02 | Medium | یکتایی دسته بدون UNIQUE DB | ایندکس/قفل یا generated unique |
| F-DATA-03 | Medium | restore دسته ≠ restore تراکنش‌ها | مستندسازی UX یا restore آبشاری اختیاری |
| F-DATA-04 | Medium | drift FK CASCADE vs SET NULL | هم‌تراز کردن DB با migration |
| F-DATA-05 | Medium | Factory با category_id null | حذف از prod یا تعمیر seed |

## Correctness / Auth behavior

| ID | موضوع | اقدام |
|----|--------|--------|
| F-AUTH-01 | remember فقط در Base_Controller | restore مشترک برای API/profile |
| F-AUTH-02 | Transaction route id نادیده گرفته می‌شود | امضای متد را با `$id` هم‌راستا کن |
| F-AUTH-03 | HTTP method روی چند route محدود نیست | `['POST']` صریح |
| F-AUTH-04 | برچسب «این ماه» در UI در برابر بازه ۳۰ روز | هم‌راستا کردن copy یا منطق |

## Architecture / Maintainability

| ID | موضوع | اقدام |
|----|--------|--------|
| F-ARCH-01 | Registration_policy مرده | استفاده یا حذف |
| F-ARCH-02 | Dashboard بدون service | اختیاری برای یکنواختی |
| F-ARCH-03 | User_policy = validation | تغییر نام یا جداسازی |
| F-ARCH-04 | بدون تست خودکار | شروع از تست بودجه و authz |

## Performance

| ID | موضوع | اقدام |
|----|--------|--------|
| F-PERF-01 | نبود ایندکس مرکب تراکنش‌ها | اضافه کردن ایندکس |
| F-PERF-02 | دارایی‌های حجیم apexcharts | بیلد/CDN هدفمند |

## Testing / Docs

| ID | موضوع |
|----|--------|
| F-TEST-01 | هیچ suite تست اپ وجود ندارد |
| F-DOC-01 | این بسته `docs/codebase-audit/` اولین مستند معماری جامع است |

---

## ترتیب عملی رفع

1. بستن Migrate/Factory در production + حفاظت secrets  
2. CSRF + محدودسازی متد HTTP  
3. قفل بودجه + تست‌های مالی  
4. یکنواخت‌سازی remember/session  
5. UNIQUE/ایندکس DB  
6. پاکسازی policy مرده و route تست  
