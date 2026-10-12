# ۴. مرجع اجرای تک‌به‌تک Routeها

جزئیات مشترک احراز هویت و JSON در `03-feature-execution-flows.md` توضیح داده شده است. اینجا هر route با قرارداد یکتا و ارجاع به لایه مشترک آمده است.

---

## قراردادهای مشترک

### `Base_API_Controller::json`

- فایل: `application/controllers/api/Base_API_Controller.php`
- `http_response_code($code)` + `json_encode(..., JSON_UNESCAPED_UNICODE)` + `exit`

### `Base_API_Controller::auth_user`

- خواندن `session.user_id`؛ نبود → JSON 401 و exit

### `Base_Controller::authCheck`

- نبود `user_id` → `redirect('login')`
- قبل از آن: `Auth_service::restore_remembered_session()`

---

## صفحات وب

### W1 — `GET /` / `Home::index`

| مورد | مقدار |
|------|--------|
| تعریف | default_controller + دسترسی `/home` |
| Middleware | `Base_Controller` |
| پاسخ | View `home.php` (summary + transactions + chart + category manager) |
| تست | ندارد |

### W2 — `GET /login` — `Auth::login`

بدون auth؛ view `login.php`. Remember-me در این کنترلر restore نمی‌شود.

### W3 — `GET /signup` — `Auth::register`

بدون auth؛ view `signup.php`.

### W4 — `GET /profile` — `Auth::profile`

اگر session نباشد redirect login؛ در غیر این صورت `profile.php`. **توجه:** از `Base_Controller` ارث نمی‌برد → remember cookie را restore نمی‌کند.

### W5 — `GET /test` — `Home::test`

نیاز به login؛ view آزمایشی `vue.test.php`.

---

## Auth API

### A1 — `POST /api/auth/register`

| لایه | جزئیات |
|------|--------|
| Validation | `Auth_validator::register` |
| Service | `Auth_service::register` |
| DB | INSERT users + budgets (transaction) |
| Frontend | `auth/register_card.php` |
| شکست‌ها | 400 validation؛ duplicate email/name؛ خطا در ایجاد |

### A2 — `POST /api/auth/login`

| لایه | جزئیات |
|------|--------|
| Validation | ندارد (فقط سرویس) |
| Service | `Auth_service::login` |
| Side effects | session + optional remember cookie/token |
| Frontend | `auth/login_card.php` |
| شکست‌ها | ایمیل/رمز اشتباه؛ `account_deleted` |

### A3 — `POST /api/auth/logout`

بدون `auth_user`؛ همیشه تلاش برای پاک کردن remember + session. Frontend: `navbar.php`.

### A4 — `POST /api/auth/restore`

بازیابی حساب soft-deleted. Frontend: همان login app.

### A5 — `/api/user/delete` (متد HTTP محدود نشده)

نیاز به `auth_user`؛ body: `password`. Soft delete + destroy session. Frontend: `profile/info_card.php`.

**مشاهده امنیتی:** محدودیت متد POST در routes نیست.

---

## Profile API

### P1 — `GET /api/profile`

`Profile_service::get` — بدون password. Caller: `info_card.php` `loadProfile`.

### P2 — `POST /api/profile/update`

ورودی: post fields + `$_FILES`. Policy/validation ترکیبی در `User_policy` + uniqueness در سرویس + upload. به‌روزرسانی session.

### P3 — `POST /api/profile/password`

فیلدها: `current_password`, `new_password`, `new_password_confirm`. عدم enforce حداقل طول در سرویس (برخلاف ثبت‌نام).

---

## Dashboard / Chart

### D1 — `GET /api/dashboard`

منطق داخل کنترلر؛ مدل‌های Budget و Transaction. بدون policy جدا (فقط auth_user).

پاسخ نمونه ساختگی:

```json
{
  "status": true,
  "budget_amount": 1500000,
  "monthly_income": 2000000,
  "monthly_expense": 500000
}
```

### C1 — `GET /api/chart?period=monthly|weekly`

پیش‌فرض period=`monthly`. خروجی `data: [{day, income, expense, balance}, ...]`.

---

## Transaction API

### T1 — `GET /api/transaction`

Query: page, search, type, category_id, from_date, to_date, sort.  
پاسخ: transactions + pagination (limit=5).

### T2 — `POST /api/transaction/create`

Body: category_id, title, amount, type, transaction_date, description.  
Validator + validate_category + Transaction_model::create (با change_budget).

### T3 — `/api/transaction/update/(:num)`

- **URI id:** به متد پاس داده می‌شود ولی امضای PHP کنترلر پارامتر ندارد / استفاده نمی‌کند.
- **شناسه واقعی:** `post('id')`
- Validator همان create
- Policy can_update روی مالکیت

### T4 — `/api/transaction/delete/(:num)`

شناسه از POST `id`؛ soft delete + reverse budget.

### T5 — `GET /api/transaction/deleted`

لیست soft-deleted ۳۰ روز اخیر با `days_left`.

### T6 — `POST /api/transaction/restore`

Body: `id`. چک دسته فعال؛ restore + apply budget.

---

## Category API

### K1 — `GET /api/categories`

لیست فعال کاربر. Callers: category_manager + ta_index `loadCategoryOptions`.

### K2 — `POST /api/categories/create`

title, type ∈ {income,expense}؛ uniqueness فعال.

### K3 — `/api/categories/update/(:num)`

$id از مسیر؛ policy can_update؛ uniqueness با except id.

### K4 — `/api/categories/delete/(:num)`

soft delete دسته + تراکنش‌های مرتبط + recalculate_budget.

### K5 — `GET /api/categories/deleted`

### K6 — `POST /api/categories/restore/(:num)`

برخورد با عنوان فعال → 400.

---

## ابزار توسعه

### X1/X2 — Migrate

بدون auth. `migration_enabled=TRUE` در config. خطر production.

### X3/X4 — Factory

`transactions($count)`: حذف تراکنش‌های با description مشخص، insert_batch تصادفی ۳۰ روز، recalculate_budget. category_id=null در داده تولیدشده — ممکن است با NOT NULL فعلی migration ناسازگار باشد (**ریسک صحت داده**).

---

## نگاشت سریع Frontend → Route

| Caller | Routeها |
|--------|---------|
| `register_card.php` | A1 |
| `login_card.php` | A2, A4 |
| `navbar.php` | A3 |
| `info_card.php` | P1, P2, P3, A5 |
| `summary.php` | D1 |
| `line_chart.php` | C1 |
| `ta_index.php` | T1–T6, K1 |
| `category_manager.php` | K1–K6 |
