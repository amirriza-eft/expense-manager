# ۹. تست و راستی‌آزمایی

## وضعیت کشف‌شده

| نوع تست | وجود در ریپو | توضیح |
|---------|---------------|--------|
| Unit PHP | خیر | PHPUnit config/tests اپ یافت نشد |
| Feature/HTTP | خیر | — |
| Frontend unit | خیر | — |
| E2E | خیر | — |
| CI pipeline | مشاهده نشد | — |
| Postman | جزئی | `.postman/resources.yaml` و `postman/globals/` محتوایی حداقلی دارند؛ collection کامل API نیست |

تنها فایل با نام test مرتبط با اپ: `application/views/vue.test.php` (صفحه آزمایشی UI، نه تست خودکار) و route `/test`.

کتابخانه `system/libraries/Unit_test.php` بخشی از فریم‌ورک CI است و برای این پروژه suite تعریف نشده.

---

## دستورات اجراشده در ممیزی

| دستور | نتیجه |
|-------|--------|
| بازرسی استاتیک فایل‌ها | انجام شد |
| `composer test` / `phpunit` | suite موجود نبود — اجرا نشد |
| migration / seed | **عمداً اجرا نشد** (read-only audit؛ تغییر state ممنوع) |
| npm test | اسکریپت تست تعریف نشده |

---

## پوشش رفتاری (استنباطی)

| جریان | تست خودکار |
|-------|------------|
| Register / login / remember | ندارد |
| Category uniqueness | ندارد |
| Transaction budget math | ندارد |
| Category delete → recalculate | ندارد |
| Profile upload | ندارد |
| Authorization IDOR | ندارد |

---

## اولویت تست‌های مفقود

1. **Financial correctness:** create/update/delete/restore تراکنش و حذف دسته → مقدار `budgets.amount`  
2. **Authz:** کاربر A نتواند تراکنش/دسته B را تغییر دهد  
3. **Uniqueness:** عنوان دسته فعال تکراری رد شود؛ بعد از soft-delete مجاز باشد  
4. **Auth:** login حساب حذف‌شده کد `account_deleted`؛ restore  
5. **Validation:** amount و category_id نامعتبر  
6. **Security smoke:** Migrate بدون auth باید در production 404/403 شود (بعد از remediation)

---

## محدودیت راستی‌آزمایی runtime

بدون محیط DB تست ایزوله و suite، رفتار واقعی FK (CASCADE vs SET NULL) و ستون `full_name_normalized` فقط از dump قدیمی قابل حدس است و به‌عنوان **Unverified runtime** علامت خورده است.
