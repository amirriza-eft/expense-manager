# ۲. فهرست کامل Routeها

**منبع اصلی:** `application/config/routes.php`  
**Routeهای ضمنی CI:** کنترلرهایی که در `routes.php` نیستند ولی از طریق الگوی پیش‌فرض `/{controller}/{method}` در دسترس‌اند.

در CodeIgniter 3، «نام route» به سبک Laravel وجود ندارد؛ شناسه همان URI و متد HTTP است.

---

## جدول خلاصه Routeهای اپلیکیشن

### صفحات وب (HTML)

| # | Method | URI | کنترلر::متد | Auth | View |
|---|--------|-----|-------------|------|------|
| W1 | GET | `/` یا `/home` | `Home::index` | بله (`Base_Controller`) | `home.php` |
| W2 | GET | `/login` | `Auth::login` | خیر | `login.php` |
| W3 | GET | `/signup` | `Auth::register` | خیر | `signup.php` |
| W4 | GET | `/profile` | `Auth::profile` | چک session در متد | `profile.php` |
| W5 | GET | `/test` | `Home::test` | بله (`Base_Controller`) | `vue.test.php` |

### API — Auth

| # | Method | URI | کنترلر::متد | Auth | Validation |
|---|--------|-----|-------------|------|------------|
| A1 | POST | `/api/auth/register` | `Auth_API_Controller::register` | خیر | `Auth_validator::register` |
| A2 | POST | `/api/auth/login` | `Auth_API_Controller::login` | خیر | بدون validator اختصاصی |
| A3 | POST | `/api/auth/logout` | `Auth_API_Controller::logout` | خیر (session اختیاری) | — |
| A4 | POST | `/api/auth/restore` | `Auth_API_Controller::restore` | خیر | — |
| A5 | *any* | `/api/user/delete` | `Auth_API_Controller::delete_account` | `auth_user()` | پسورد در سرویس |

### API — Profile

| # | Method | URI | کنترلر::متد | Auth |
|---|--------|-----|-------------|------|
| P1 | GET | `/api/profile` | `Profile_API_Controller::get` | بله |
| P2 | POST | `/api/profile/update` | `Profile_API_Controller::update` | بله |
| P3 | POST | `/api/profile/password` | `Profile_API_Controller::change_password` | بله |

### API — Dashboard / Chart

| # | Method | URI | کنترلر::متد | Auth |
|---|--------|-----|-------------|------|
| D1 | GET | `/api/dashboard` | `Dashboard_API_Controller::summary` | بله |
| C1 | GET | `/api/chart` | `Chart_API_Controller::chart` | بله |

### API — Transactions

| # | Method | URI | کنترلر::متد | Auth | Validation |
|---|--------|-----|-------------|------|------------|
| T1 | GET | `/api/transaction` | `Transaction_API_Controller::index` | بله | query params |
| T2 | POST | `/api/transaction/create` | `::create` | بله | `Transaction_validator::create` |
| T3 | *any* | `/api/transaction/update/(:num)` | `::update` | بله | `Transaction_validator::update` |
| T4 | *any* | `/api/transaction/delete/(:num)` | `::delete` | بله | — |
| T5 | GET | `/api/transaction/deleted` | `::deleted` | بله | — |
| T6 | POST | `/api/transaction/restore` | `::restore` | بله | — |

### API — Categories

| # | Method | URI | کنترلر::متد | Auth |
|---|--------|-----|-------------|------|
| K1 | GET | `/api/categories` | `Category_API_Controller::index` | بله |
| K2 | POST | `/api/categories/create` | `::create` | بله |
| K3 | *any* | `/api/categories/update/(:num)` | `::update` | بله |
| K4 | *any* | `/api/categories/delete/(:num)` | `::delete` | بله |
| K5 | GET | `/api/categories/deleted` | `::deleted` | بله |
| K6 | POST | `/api/categories/restore/(:num)` | `::restore` | بله |

### ابزار توسعه (ضمنی، بدون تعریف در routes.php)

| # | Method | URI محتمل | کنترلر::متد | Auth در کد |
|---|--------|-----------|-------------|------------|
| X1 | GET | `/migrate/latest` | `Migrate::latest` | **خیر** |
| X2 | GET | `/migrate/rollback` | `Migrate::rollback` | **خیر** |
| X3 | GET | `/factory/test` | `Factory::test` | خیر |
| X4 | GET | `/factory/transactions/{count}` | `Factory::transactions` | session user_id لازم |

**جمع routeهای اپ تعریف‌شده صریح در `routes.php`:** ۲۷ (۵ صفحه + ۲۲ API)  
**جمع با ابزار توسعه ضمنی:** ۳۱+

---

## Middleware / فیلتر واقعی CI3

CodeIgniter 3 middleware گروهی Laravel ندارد. جایگزین‌ها:

| مکانیزم | محل | رفتار |
|---------|-----|--------|
| Autoload session/DB | `autoload.php` | همه درخواست‌ها |
| `Base_Controller::authCheck` | `Home` (+ `test`) | redirect به `/login` اگر `user_id` نباشد؛ همچنین `restore_remembered_session()` |
| `Base_API_Controller::auth_user` | اکثر APIها | JSON 401 اگر session خالی |
| CSRF | `config['csrf_protection']=FALSE` | **غیرفعال** |
| Hooks | `enable_hooks=FALSE` | غیرفعال |

---

## یافته‌های Route

### ۱. متد HTTP نامحدود (`*any*`)

برای این URIها فقط مسیر تعریف شده، نه `['POST']`:

- `/api/transaction/update/(:num)`
- `/api/transaction/delete/(:num)`
- `/api/categories/update/(:num)`
- `/api/categories/delete/(:num)`
- `/api/user/delete`

فرانت‌اند فقط POST می‌فرستد، اما از نظر فریم‌ورک GET هم ممکن است به همان متد برسد (بسته به تنظیمات سرور و اینکه POST body خالی باشد).

### ۲. پارامتر URI در مقابل body

- Route آپدیت/حذف تراکنش و دسته `:num` را به متد پاس می‌دهد.
- `Transaction_API_Controller::update/delete` از `$this->input->post('id')` استفاده می‌کنند و آرگومان مسیر را به‌عنوان `$id` نمی‌گیرند — **پارامتر مسیر عملاً نادیده گرفته می‌شود** (فرانت هم `id` را در body می‌فرستد).
- `Category_API_Controller::update/delete/restore` آرگومان `$id` مسیر را استفاده می‌کنند.

### ۳. Routeهای بدون caller فرانت کشف‌شده

| Route | توضیح |
|-------|--------|
| `/test` (`Home::test`) | view آزمایشی Vue |
| `/migrate/*`, `/factory/*` | ابزار dev |

### ۴. Routeهای خطرناک عمومی

| Route | ریسک |
|-------|------|
| `Migrate::latest/rollback` | تغییر اسکیما بدون احراز هویت |
| `Factory::transactions` | تولید داده اگر session فعال باشد |
| همه APIهای mutating با CSRF خاموش | آسیب‌پذیری CSRF در صورت session فعال |

### ۵. یادآوری remember-me

فقط `Base_Controller` (صفحات Home) `restore_remembered_session()` را صدا می‌زند. API و `Auth::profile` این کار را نمی‌کنند. جزئیات در `05-security-audit.md`.

---

## گروه‌بندی ماژول برای اسناد بعدی

جزئیات قرارداد درخواست/پاسخ هر route در:

- `03-feature-execution-flows.md` (جریان‌های اصلی)
- `04-route-execution-reference.md` (مرجع تک‌به‌تک)
