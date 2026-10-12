# ۸. معماری، کیفیت و عملکرد

ارزیابی بر اساس کد موجود — نه معماری ایده‌آل Laravel.

---

## الگوی معماری واقعی

**نقاط قوت**

- جداسازی نسبی Controller → Service → Model در Auth/Category/Transaction/Profile
- Policies برای مالکیت (هرچند ساده)
- Validators برای دو حوزه حساس
- تراکنش‌های DB روی عملیات بودجه

**نقاط ضعف / ناسازگاری**

| مورد | توضیح |
|------|--------|
| Dashboard/Chart بدون Service | منطق در کنترلر/مدل پخش است |
| `User_policy` مخلوط validation و authorization | نام‌گذاری گمراه‌کننده |
| `Registration_policy` مرده | هرگز صدا زده نمی‌شود |
| DI دستی با require_once | تکرار boilerplate در هر کنترلر |
| Transaction controller امضای متد با route param هم‌خوان نیست | |
| صفحات Auth خارج از Base_Controller | رفتار auth ناهماهنگ |

---

## عملکرد

| یافته | شدت | مسیر کد | توضیح |
|-------|------|---------|--------|
| PERF-01 صفحه‌بندی سخت ۵تایی | Low | `Transaction_service` limit=5 | UX؛ نه باگ |
| PERF-02 داشبورد چند query جدا | Low | `Dashboard_API_Controller` | ۳ query؛ قابل قبول در مقیاس کوچک |
| PERF-03 chart حلقه PHP روزانه | Low | `Budget_model::get_chart_data` | حداکثر ۳۰ روز |
| PERF-04 نبود ایندکس مرکب تراکنش | Medium | migrations | فیلتر user+deleted+date مکرر است |
| PERF-05 N+1 | — | لیست تراکنش join دارد | N+1 مشهود نیست |
| PERF-06 بدون cache | Info | — | برای این مقیاس لازم نیست |
| PERF-07 ApexCharts assetهای حجیم در ریپو | Info | `public/assets/js/apexcharts` | بیلد بهینه نشده |

---

## صحت مالی (بودجه)

| یافته | شدت | توضیح |
|-------|------|--------|
| FIN-01 | Medium | بودجه denormalized است؛ هر باگ در change_budget موجودی را منحرف می‌کند تا recalculate |
| FIN-02 | Medium | حذف دسته recalculate می‌کند ولی soft-delete تراکنش‌ها بدون مسیر restore دسته‌جمعی است |
| FIN-03 | Low | بازه «ماهانه» در UI برچسب «این ماه» دارد ولی کد ۳۰ روز غلتان است |
| FIN-04 | Medium | race روی دو تراکنش همزمان بدون قفل ردیف budget |

کوچک‌ترین بهبود عملی: قبل از update بودجه `SELECT ... FOR UPDATE` روی budgets در همان transaction.

---

##Maintainability

- پیام‌های فارسی پراکنده در سرویس‌ها — بدون لایه i18n
- تکرار الگوی JSON status/message
- تست خودکار غایب → رگرسیون سخت
- `helper_helper.php` فقط `dd()` — کم‌ارزش
- `vue.test.php` و route `/test` باید از production حذف شوند

---

## پیشنهادهای هدفمند (نه rewrite)

1. یک `Base_Authenticated_API` که remember restore + auth_user را یکسان کند  
2. محدود کردن HTTP method در همه routeهای mutating  
3. همسو کردن `Transaction_API_Controller::update($id)` با پارامتر مسیر  
4. فعال CSRF یا توکن معادل  
5. ایندکس DB برای تراکنش‌ها و UNIQUE شرطی/اپ+قفل برای عنوان دسته  
6. خارج کردن Migrate/Factory از web در production  
