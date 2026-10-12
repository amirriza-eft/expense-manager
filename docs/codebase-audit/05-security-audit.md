# ۵. ممیزی امنیتی

دامنه: احراز هویت session، authorization مالکیت، CSRF، آپلود، endpointهای خطرناک، افشای داده.  
**اصل:** فقط یافته‌های مرتبط با کد واقعی گزارش شده‌اند. مقدار secret واقعی چاپ نمی‌شود.

---

## SEC-001 — CSRF غیرفعال روی همه درخواست‌های mutating

| فیلد | مقدار |
|------|--------|
| Severity | **High** |
| Confidence | High |
| محل | `application/config/config.php` → `$config['csrf_protection'] = FALSE` |
| مسیر آسیب | مهاجم سایت دیگر کاربر لاگین‌شده را به `POST /api/transaction/create` یا delete/update/profile/logout هدایت می‌کند |
| پیش‌شرط | قربانی cookie session معتبر دارد؛ SameSite=Lax کمی حملات POST cross-site کلاسیک را سخت‌تر می‌کند ولی CSRF همچنان برای سناریوهای هم‌سایت/برخی جریان‌ها و ابزارها مطرح است |
| تأثیر | ایجاد/حذف تراکنش، تغییر پروفایل، خروج، حذف حساب (با دانستن/فریب برای پسورد در حذف حساب) |
| Remediation | فعال‌سازی CSRF CI یا توکن سفارشی برای APIهای session-based؛ ارسال توکن از فرانت |
| تست پیشنهادی | تست feature که بدون توکن 403 بدهد |

---

## SEC-002 — کنترلرهای Migrate بدون احراز هویت

| فیلد | مقدار |
|------|--------|
| Severity | **Critical** (اگر در محیط در دسترس عمومی باشد) |
| Confidence | High |
| محل | `application/controllers/Migrate.php` |
| مسیر | `GET /migrate/latest` یا `/migrate/rollback` بدون session check |
| تأثیر | تخریب/تغییر اسکیما DB |
| Remediation | حذف از deploy، محدودسازی IP، CLI-only، یا auth سخت + غیرفعال کردن `migration_enabled` در production |
| وضعیت config | `migration_enabled = TRUE` در `migration.php` |

---

## SEC-003 — Factory تولید داده در دسترس از وب

| فیلد | مقدار |
|------|--------|
| Severity | Medium |
| Confidence | High |
| محل | `Factory::transactions` |
| پیش‌شرط | session لاگین |
| تأثیر | پر کردن DB، حذف تراکنش‌های با description مشخص کاربر، فشار روی بودجه |
| Remediation | محدود به ENVIRONMENT=development |

---

## SEC-004 — Remember-me فقط روی Base_Controller بازیابی می‌شود

| فیلد | مقدار |
|------|--------|
| Severity | Medium (صحت/امنیت session) |
| Confidence | High |
| محل | `Base_Controller::__construct` صدا می‌زند؛ `Base_API_Controller` و `Auth` خیر |
| تأثیر | رفتار ناهماهنگ: صفحه Home ممکن است با cookie وارد شود ولی API بلافاصله 401 بدهد؛ یا برعکس انتظار کاربر برآورده نشود |
| Remediation | فراخوانی مشترک restore در hook/base API هم |

---

## SEC-005 — کوکی session با `cookie_httponly = FALSE` در config عمومی

| فیلد | مقدار |
|------|--------|
| Severity | Medium |
| Confidence | Medium |
| محل | `config.php` `$config['cookie_httponly'] = FALSE` |
| توضیح | تنظیم عمومی cookieهای CI؛ remember_me جداگانه httponly=true ست می‌شود. اگر session cookie از این تنظیم پیروی کند، XSS می‌تواند session را بدزدد |
| Remediation | `cookie_httponly=TRUE` و `cookie_secure=TRUE` در HTTPS؛ کاهش XSS |

---

## SEC-006 — `encryption_key` خالی

| فیلد | مقدار |
|------|--------|
| Severity | Low–Medium |
| Confidence | High |
| محل | `$config['encryption_key'] = ''` |
| تأثیر | اگر بعداً Encryption library استفاده شود ناامن است؛ session فایل‌محور فعلی لزوماً به آن وابسته نیست |
| Remediation | تنظیم کلید قوی در env خارج از git |

---

## SEC-007 — IDOR تا حد خوبی با user_id مهار شده؛ نقاط توجه

| فیلد | مقدار |
|------|--------|
| Severity | Informational / Low |
| Confidence | High |
| شواهد مثبت | Category/Transaction policy مالکیت؛ queryهای مدل اغلب `where user_id` |
| نکته | `Transaction_model::get_by_id($id)` فیلتر user_id ندارد؛ authorization بعداً در سرویس با مقایسه `transaction->user_id` انجام می‌شود — الگوی درست است اگر همیشه از سرویس برود |
| نکته مسیر | update/delete تراکنش id مسیر را نادیده می‌گیرد و به POST متکی است — verwirrend ولی لزوماً IDOR نیست |

---

## SEC-008 — آپلود آواتار

| فیلد | مقدار |
|------|--------|
| Severity | Low–Medium |
| Confidence | Medium |
| محل | `Profile_service::update` + CI Upload |
| کنترل‌ها | allowed_types jpg/jpeg/png/webp، max_size 2048، encrypt_name |
| ریسک باقی‌مانده | سرو کردن فایل از مسیر وب (`uploads` symlink در public)؛ اطمینان از عدم اجرای PHP در uploads؛ اعتبارسنجی MIME واقعی |
| Remediation | بلاک اجرای اسکریپت در پوشه uploads در وب‌سرور |

---

## SEC-009 — افشای احتمالی credential در ریپو

| فیلد | مقدار |
|------|--------|
| Severity | High (فرآیندی) |
| Confidence | High |
| محل | `application/config/database.php` (تغییرات محلی)، `.db-backups/*.sql` حاوی داده کاربر واقعی |
| تأثیر | نشت رمز DB / هش پسورد / ایمیل در git history یا backup |
| Remediation | env-based config؛ حذف backup حساس از ریپو؛ rotate secrets |
| این سند | مقادیر secret را تکرار نمی‌کند |

---

## SEC-010 — عدم rate limit روی login/register/restore

| فیلد | مقدار |
|------|--------|
| Severity | Medium |
| Confidence | High |
| تأثیر | brute-force پسورد؛ enumeration نسبی از طریق پیام‌های متفاوت (account_deleted در مقابل wrong password) |
| Remediation | throttling، پیام یکسان برای failure، قفل موقت |

---

## SEC-۱۱ — XSS ذخیره‌شده (ارزیابی)

| فیلد | مقدار |
|------|--------|
| Severity | Low (با Vue text interpolation) |
| Confidence | Medium |
| توضیح | فیلدهای کاربر در Vue غالباً با `{{ }}` رندر می‌شوند (escape پیش‌فرض). در PHP navbar برای نام از `html_escape` استفاده شده. `v-html` در مسیرهای اصلی تراکنش برای مبلغ از helper ساخته می‌شود — باید مراقب بود محتوای خام DB وارد v-html نشود |
| شواهد | `amountHtml` از `formatAmount` عددی می‌سازد |

---

## SEC-012 — Registration_policy استفاده‌نشده

Informational: سیاست «کاربر لاگین‌شده نتواند register کند» enforce نمی‌شود — کاربر لاگین‌شده از نظر API می‌تواند دوباره register کند (حساب جدید).

---

## موارد غایب که آسیب‌پذیری نیستند مگر با زمینه

- نبود JWT/Sanctum: طراحی session-cookie است، نه نقص به‌خودی‌خود.
- نبود نقش ادمین: اپ تک‌کاربره per-account است.
- SQL Injection: عمدتاً Query Builder با bind؛ نقاط `false` escape در TRIM/SUM نیاز به دقت دارند ولی ورودی‌ها escape یا cast می‌شوند.

---

## خلاصه اولویت امنیتی

1. SEC-002 Migrate عمومی  
2. SEC-001 CSRF  
3. SEC-009 Secrets/backups در ریپو  
4. SEC-010 Rate limit  
5. SEC-004/005 Session cookie hardening  
6. SEC-003 Factory در production  
