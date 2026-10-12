# ممیزی کامل کدبیس — حساب‌یار (expense-manager)

**نوع کار:** فقط مستندسازی و تحلیل خواندنی (بدون تغییر کد اپ)  
**استک واقعی کشف‌شده:** CodeIgniter **3.1.13** + PHP views + Vue 3 (CDN) + Axios + MySQL — **نه Laravel**

---

## دامنه ممیزی

- بک‌اند: routes، controllers، services، models، policies، validators، migrations، config امنیتی/session  
- فرانت: صفحات PHP و جزیره‌های Vue که API را صدا می‌زنند  
- امنیت، یکپارچگی داده بودجه، کیفیت، تست (غایب)

---

## فهرست اسناد

| # | فایل | محتوا |
|---|------|--------|
| 01 | [01-project-overview.md](./01-project-overview.md) | نمای کلی، استک، نقشه دایرکتوری |
| 02 | [02-route-inventory.md](./02-route-inventory.md) | فهرست کامل routeها |
| 03 | [03-feature-execution-flows.md](./03-feature-execution-flows.md) | جریان‌های end-to-end |
| 04 | [04-route-execution-reference.md](./04-route-execution-reference.md) | مرجع تک‌route |
| 05 | [05-security-audit.md](./05-security-audit.md) | ممیزی امنیتی |
| 06 | [06-database-and-data-lifecycle.md](./06-database-and-data-lifecycle.md) | اسکیما و چرخه داده |
| 07 | [07-frontend-and-api-contracts.md](./07-frontend-and-api-contracts.md) | فرانت و قرارداد API |
| 08 | [08-quality-and-performance.md](./08-quality-and-performance.md) | کیفیت و عملکرد |
| 09 | [09-tests-and-verification.md](./09-tests-and-verification.md) | تست‌ها |
| 10 | [10-developer-learning-guide.md](./10-developer-learning-guide.md) | راهنمای یادگیری |
| 11 | [11-architecture-diagrams.md](./11-architecture-diagrams.md) | دیاگرام‌ها |
| 12 | [12-prioritized-findings.md](./12-prioritized-findings.md) | یافته‌های اولویت‌دار |
| 13 | [13-inspection-coverage.md](./13-inspection-coverage.md) | پوشش بازرسی |

---

## ترتیب مطالعه پیشنهادی

1. `01` نمای کلی  
2. `11` دیاگرام‌ها  
3. `02` + `03` routeها و جریان‌ها  
4. `06` دیتابیس و بودجه  
5. `05` + `12` امنیت و اولویت‌ها  
6. `07` + `10` فرانت و یادگیری روزمره  
7. `04` به‌عنوان مرجع هنگام دیباگ  
8. `08` + `09` + `13` کیفیت، تست، پوشش  

---

## خلاصه یافته‌های بحرانی

1. **Migrate از وب بدون احراز هویت** در حالی که migration فعال است  
2. **CSRF غیرفعال** روی APIهای session-based  
3. **credential / backup حساس** در درخت پروژه (مقادیر در این docs تکرار نشده)  
4. **بودجه denormalized** بدون قفل همزمانی  
5. **نبود تست خودکار** برای منطق مالی  

---

## مهم‌ترین جریان‌های اجرا

1. ثبت‌نام → user + budget  
2. ورود / remember-me / خروج  
3. CRUD دسته + soft-delete آبشاری تراکنش‌ها + recalculate budget  
4. CRUD تراکنش با `change_budget`  
5. داشبورد ۳۰روزه + نمودار  
6. پروفایل / آواتار / تغییر رمز / حذف حساب  

---

## آمار پوشش (تقریبی)

| مورد | مقدار |
|------|--------|
| Routeهای صریح در routes.php | ۲۷ (۵ صفحه + ۲۲ API) |
| Route/ابزار ضمنی اضافه‌شده | Migrate + Factory |
| جریان‌های اصلی مستندشده | ۸+ (auth، profile، category، transaction، dashboard، chart، delete/restore account) |
| سرویس‌ها | ۴ |
| مدل‌ها | ۵ |
| تست اجراشده | ۰ (suite موجود نبود) |

---

## محدودیت‌ها

- راستی‌آزمایی runtime روی DB زنده انجام نشد  
- اختلاف احتمالی dump قدیمی با migration فعلی مستند شد  
- `system/` و `vendor/` به‌عنوان فریم‌ورک عمیق بررسی نشدند  

جزئیات: `13-inspection-coverage.md`
