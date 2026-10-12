# ۷. معماری فرانت‌اند و قراردادهای API

فرانت‌اند یک SPA جدا نیست. الگوی واقعی: **PHP views + چند Vue 3 app جزیره‌ای + Axios هم‌منشأ**.

---

## نقاط ورود UI

| صفحه URL | View ریشه | Vue roots |
|----------|-----------|-----------|
| `/login` | `login.php` | `#login-app` |
| `/signup` | `signup.php` | `#register-app` |
| `/` home | `home.php` | `#navbar-app`, `#dashboard-summary`, `#transactions-app`, `#categories-app`, `#finance-chart-app` |
| `/profile` | `profile.php` | `#profile-app` (+ navbar) |

کتابخانه‌های مشترک از `components/navbar.php`: Vue 3 global، Axios، Bootstrap 5 RTL، Vazirmatn، `format.js`.

تراکنش‌ها jQuery + persian-datepicker را جداگانه در `ta_index.php` لود می‌کنند.

---

## مدیریت state

- بدون Vuex/Pinia؛ state محلی هر `createApp`.
- ارتباط بین‌اپ‌ها با **توابع global روی `window`**:
  - `loadDashboardSummary`
  - `reloadFinanceChart`
  - `reloadCategoryOptions`
  - `reloadTransactions`
- Session کاربر در PHP session رندر اولیه navbar (آواتار/نام)؛ بعد از آپدیت پروفایل DOM آواتار دستی به‌روز می‌شود.

---

## نگاشت Endpoint ↔ Frontend

| API | Frontend caller | Payload | نکات |
|-----|-----------------|---------|------|
| POST `/api/auth/register` | `register_card.php` | FormData | خطای 400 از catch خوانده می‌شود |
| POST `/api/auth/login` | `login_card.php` | FormData | remember_me 0/1 |
| POST `/api/auth/restore` | `login_card.php` | FormData | |
| POST `/api/auth/logout` | `navbar.php` | خالی | |
| POST `/api/user/delete` | `info_card.php` | URLSearchParams password | |
| GET `/api/profile` | `info_card.php` | — | |
| POST `/api/profile/update` | `info_card.php` | FormData + file | |
| POST `/api/profile/password` | `info_card.php` | FormData | |
| GET `/api/dashboard` | `summary.php` | — | |
| GET `/api/chart` | `line_chart.php` | period | AbortController |
| GET `/api/transaction` | `ta_index.php` | query filters | تاریخ شمسی→میلادی |
| POST create/update tx | `ta_index.php` | FormData | update URL با id ولی id در body |
| POST delete tx | `ta_index.php` | URLSearchParams id | URL هم id دارد |
| GET deleted / POST restore tx | `ta_index.php` | | |
| GET/POST categories* | `category_manager.php` + options در ta_index | | |

---

## قراردادهای داده فرانت‌اند

### تاریخ

- نمایش: شمسی `YYYY/MM/DD` با ارقام فارسی (`formatPersianDate`)
- ارسال API: میلادی `YYYY-MM-DD` (`convertPersianToGregorian`)

### مبلغ

- ورودی UI با جداکننده هزارگان فارسی
- ارسال: فقط رقم انگلیسی (`getAmountInputValue`)

### Content-Type

ترکیب FormData، URLSearchParams، و querystring — بک‌اند همه را از `$this->input->post/get` می‌خواند (CI).

---

## ناسازگاری‌ها و ریسک‌های UI↔API

| ID | مسئله | شواهد |
|----|--------|--------|
| FE-01 | بعضی catchها پیام بک‌اند را نشان نمی‌دهند (login) | `login_card.php` catch عمومی |
| FE-02 | delete تراکنش در catch فقط alert عمومی | `ta_index.php` |
| FE-03 | پارامتر مسیر update/delete تراکنش تزئینی است | کنترلر از post id می‌خواند |
| FE-04 | لیست تراکنش وقتی `transactions.length===0` کل pagination را صفر می‌کند حتی اگر status true | شرط `data.transactions.length` |
| FE-05 | اعتبارسنجی فرانت جایگزین بک‌اند نیست؛ بعضی فرم‌ها (login) validator سرور ندارند | Auth login |
| FE-06 | صفحه profile بدون Base_Controller → remember restore نمی‌شود | `Auth.php` |

---

## احراز هویت از دید مرورگر

1. Login موفق → Set-Cookie `ci_session` (و اختیاری `remember_me`)
2. Axios پیش‌فرض same-origin cookies را می‌فرستد (نیازی به bearer نیست)
3. CSRF header ارسال نمی‌شود چون بک‌اند CSRF ندارد
4. 401 API: بیشتر UIها فقط console/alert؛ redirect خودکار سراسری به login ندارند

---

## فایل‌های استاتیک مرتبط

- `public/assets/js/format.js` — فقط نمایش؛ نباید برای امنیت به آن اتکا کرد
- `public/assets/css/app.css`, `transactions.css`, `star-background.css`
- `public/uploads` → symlink به `../uploads`
