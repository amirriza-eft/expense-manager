# ۱۰. راهنمای یادگیری توسعه‌دهنده (مختص این ریپو)

این راهنما مفاهیم را با مثال‌های **CodeIgniter 3 + Vue islands** همین پروژه توضیح می‌دهد، نه Laravel.

---

## ۱. چطور یک route را پیدا کنم؟

1. باز کردن `application/config/routes.php`
2. یافتن URI (مثلاً `api/categories/create`)
3. مقدار سمت راست مثل `api/Category_API_Controller/create` یعنی:
   - فایل: `application/controllers/api/Category_API_Controller.php`
   - متد: `create`

اگر در routes نبود، الگوی پیش‌فرض CI: `/ControllerName/methodName`.

---

## ۲. چطور به کنترلر برسم؟

`require_once` برای Base و سرویس‌ها را در `__construct` ببین. مثال API:

`Category_API_Controller` → `auth_user()` → `category_service->...` → `$this->json(...)`.

---

## ۳. Middleware کجاست؟

CI3 middleware لاراول ندارد. معادل‌ها:

| نیاز | مکان این پروژه |
|------|----------------|
| اجبار login صفحه | `Base_Controller::authCheck` |
| اجبار login API | `Base_API_Controller::auth_user` |
| CSRF | خاموش در config |
| Remember restore | فقط `Base_Controller` |

---

## ۴. Validation کجاست؟

| حوزه | کلاس | فراخوانی |
|------|------|----------|
| Register | `Auth_validator` | کنترلر قبل از سرویس |
| Transaction create/update | `Transaction_validator` | کنترلر |
| Category | داخل `Category_service` | بدون validator کلاس |
| Profile | `User_policy` + سرویس | ترکیبی |
| Login | تقریباً فقط سرویس | |

شکست form_validation → معمولاً HTTP 400 JSON.

---

## ۵. Authorization کجاست؟

Policies در `application/policies/` — **خودکار اعمال نمی‌شوند**. باید سرویس صدا بزند:

- `Category_policy::can_update($user_id, $category)`
- `Transaction_policy::can_delete(...)`

تفاوت با Authentication: auth = «کیستی؟» (session user_id)؛ authz = «آیا این رکورد مال توست؟».

---

## ۶. از کنترلر به سرویس

الگوی رایج:

```php
$this->category_service = new Category_service($this->Category_model, $policy);
```

سرویس آرایه `['status'=>..., 'message'=>..., 'code'=>...]` برمی‌گرداند؛ کنترلر code را به HTTP می‌دهد.

---

## ۷. مدل و Query Builder

مدل‌ها Eloquent نیستند؛ `CI_Model` + `$this->db->where(...)->get($table)`.

مثال soft delete: `where('deleted_at IS NULL', null, false)`.

---

## ۸. تراکنش DB

مثال خوب: `Transaction_model::create/update/delete` با `trans_start` / `trans_complete` و `change_budget`.

اگر وسط کار budget fail شود → rollback.

---

## ۹. Events / Jobs

وجود ندارند. همه چیز همگام در همان درخواست HTTP است.

---

## ۱۰. پیدا کردن caller فرانت

در `application/views` جستجو کنید:

```text
site_url('api/...')
```

یا `axios.post` / `axios.get`.

---

## ۱۱. دنبال کردن پاسخ تا UI

معمولاً:

```javascript
const data = response.data;
if (data.status) { /* به‌روز کردن state / بستن مودال / reload */ }
else { this.message = data.message }
```

برای HTTP غیر۲xx، axios به `catch` می‌رود — بعضی صفحات `error.response.data.message` را می‌خوانند.

---

## ۱۲. دیباگ درخواست شکست‌خورده

1. Network tab: وضعیت HTTP و JSON body  
2. آیا `ci_session` ارسال شده؟  
3. آیا به `auth_user` خورده (401)؟  
4. آیا validator 400 داده؟  
5. آیا policy 403 داده؟  
6. لاگ CI اگر `log_threshold` فعال شود (فعلاً 0)

---

## ۱۳. افزودن endpoint جدید مطابق قرارداد پروژه

1. متد در Model  
2. قاعده در Service (+ Policy اگر مالکیت دارد)  
3. متد در `*_API_Controller` با `auth_user`  
4. ثبت در `routes.php` با HTTP method صریح  
5. فراخوانی Axios در view مربوطه  
6. (توصیه) تست دستی + بعداً تست خودکار

---

## ۱۴. تغییر امن قاعده کسب‌وکار

مثال: یکتایی دسته — الان در `Category_service` + `title_exists` است. تغییر را همان‌جا بدهید نه فقط در فرانت.

برای بودجه: هرگز مبلغ را فقط در UI عوض نکنید؛ مسیر `Transaction_model::change_budget` یا `recalculate_budget` را در نظر بگیرید.

---

## ۱۵. واژه‌نامه مفاهیم در بستر این پروژه

| مفهوم | در این ریپو |
|--------|-------------|
| Route | ورودی `$route` در `routes.php` |
| Controller | کلاس در `application/controllers` |
| Middleware | Base_Controller / auth_user / config |
| Policy | کلاس دستی در services |
| Validation | form_validation wrappers یا ifهای سرویس |
| Service | کلاس PHP ساده بدون interface اجباری |
| Model | Query Builder روی جدول |
| Session vs Cookie | session سمت سرور + کوکی id؛ remember_me کوکی جدا با selector:validator |
| Cookie auth vs Bearer | فقط cookie/session؛ Bearer نیست |
| Sync vs Queue | فقط sync |
| Frontend validation | UX؛ منبع حقیقت بک‌اند است |

---

## مسیر پیشنهادی یادگیری یک روزه

1. `routes.php` را کامل بخوانید  
2. یک جریان login را از `login_card` تا `Auth_service` دنبال کنید  
3. یک create تراکنش را تا `change_budget` دنبال کنید  
4. حذف دسته را تا `recalculate_budget` دنبال کنید  
5. اسناد `03` و `05` را برای تصویر امنیتی بخوانید  
