# ۶. پایگاه داده و چرخه عمر داده

منابع: `application/migrations/001`–`005`، مدل‌ها، و مقایسه اختیاری با dump `.db-backups/` (بدون افشای داده حساس).

---

## دیاگرام ER (تأییدشده از migrations)

```mermaid
erDiagram
    users ||--o| budgets : has
    users ||--o{ categories : owns
    users ||--o{ transactions : owns
    users ||--o{ remember_tokens : has
    categories ||--o{ transactions : classifies

    users {
        int id PK
        varchar full_name UK
        varchar email UK
        varchar password
        varchar avatar
        datetime created_at
        datetime updated_at
        datetime deleted_at
    }

    budgets {
        int id PK
        int user_id UK FK
        decimal amount
        datetime deleted_at
    }

    categories {
        int id PK
        int user_id FK
        varchar title
        enum type
        datetime deleted_at
    }

    transactions {
        int id PK
        int user_id FK
        int category_id FK
        varchar title
        decimal amount
        enum type
        date transaction_date
        text description
        datetime deleted_at
    }

    remember_tokens {
        bigint id PK
        int user_id
        varchar selector UK
        varchar token_hash
        datetime expires_at
    }
```

---

## جداول به‌تفصیل (از migration)

### `users` — `001_create_users.php`

| ستون | نوع | قیود |
|------|-----|------|
| id | INT UNSIGNED AI | PK |
| full_name | VARCHAR(100) | UNIQUE در migration |
| email | VARCHAR(255) | UNIQUE |
| password | VARCHAR(255) | — |
| avatar | VARCHAR(255) NULL | — |
| created_at / updated_at | DATETIME | default/on update |
| deleted_at | DATETIME NULL | soft delete |

**Drift احتمالی:** dump قدیمی ستون generated `full_name_normalized` و UNIQUE روی آن را نشان می‌دهد؛ migration فعلی UNIQUE روی خود `full_name` دارد. محیط واقعی باید با `SHOW CREATE TABLE` تأیید شود (**Unverified runtime**).

### `categories` — `002`

- FK `user_id → users.id ON DELETE CASCADE`
- `type` ENUM('income','expense')
- soft delete
- **بدون UNIQUE DB روی (user_id, title, type)** — یکتایی فقط در لایه اپ (`title_exists`)

### `budgets` — `003`

- `user_id` UNIQUE + FK CASCADE
- `amount` DECIMAL(12,2) default 0
- یک بودجه per user

### `transactions` — `004`

- `category_id` NOT NULL + FK به categories **ON DELETE CASCADE** (طبق migration فعلی)
- `amount` DECIMAL(18,2)
- soft delete روی ردیف تراکنش

**Drift:** dump قدیمی `ON DELETE SET NULL` برای category نشان می‌دهد؛ migration بعداً به CASCADE تغییر کرده (`da751d1`). محیط زنده ممکن است هنوز SET NULL باشد.

### `remember_tokens` — `005`

- selector UNIQUE
- **بدون FK رسمی به users در migration** (فقط ستون user_id)
- پاک‌سازی با `delete_by_user` / `delete_by_selector` / `delete_expired` (expired در کد فراخوانی خودکار زمان‌بندی‌شده ندارد)

---

## چرخه عمر موجودیت‌ها

### User

1. **Create:** register → insert user + budget=0  
2. **Login:** session (+ optional remember token)  
3. **Update profile:** نام/ایمیل/آواتار؛ uniqueness اپلیکیشنی  
4. **Soft delete:** `deleted_at` ست می‌شود؛ remember tokens حذف سخت می‌شوند؛ session destroy  
5. **Restore:** `deleted_at=null` با verify password  
6. **Hard delete user:** فقط اگر ردیف users واقعاً DELETE شود → CASCADE بودجه/دسته/تراکنش (بسته به FK)

### Category

1. Create با title+type یکتا بین فعال‌ها  
2. Update title/type با همان قاعده  
3. Soft delete → soft delete تراکنش‌های فعال مرتبط + `recalculate_budget`  
4. Restore دسته به‌تنهایی؛ تراکنش‌ها restore خودکار نمی‌شوند  
5. اگر کاربر دسته جدید هم‌نام ساخته باشد، restore دسته قدیم شکست می‌خورد

### Transaction

1. Create → insert + `change_budget` افزایشی  
2. Update → reverse اثر قدیم + apply اثر جدید + update row (در یک DB transaction)  
3. Soft delete → reverse budget + `deleted_at`  
4. Visible in deleted list فقط ۳۰ روز  
5. Restore → نیاز به دسته فعال؛ apply budget دوباره  

### Budget

- منبع حقیقت تجمیعی؛ با هر تراکنش تغییر می‌کند یا با `recalculate_budget` از صفر محاسبه می‌شود (حذف دسته).
- **ریسک صحت:** اگر تراکنش خارج از سرویس/مدل درج شود (مثلاً Factory با شکست نیمه‌کاره) بودجه ناهماهنگ می‌شود مگر recalculate.

---

## ایندکس‌ها و عملکرد

| جدول | ایندکس‌های migration | شکاف احتمالی |
|------|---------------------|--------------|
| users | PK, UNIQUE email/full_name | — |
| categories | PK, FK user_id | ایندکس مرکب (user_id, type, deleted_at, title) برای uniqueness/lookup |
| transactions | PK, FK user, FK category | ایندکس (user_id, deleted_at, transaction_date) برای لیست/داشبورد |
| budgets | PK, UNIQUE user_id | — |
| remember_tokens | PK, UNIQUE selector, KEY user_id | FK مفقود |

---

## ناسازگاری‌ها و ریسک یکپارچگی

1. **یکتایی دسته فقط در اپ** — race condition دو درخواست همزمان می‌تواند دو ردیف هم‌نام بسازد.  
2. **حذف دسته تراکنش‌ها را soft-delete می‌کند اما restore دسته آن‌ها را برنمی‌گرداند.**  
3. **Factory** ممکن است `category_id=null` بنویسد در حالی که migration فعلی NOT NULL است.  
4. **email_exists در پروفایل** soft-deleted را هم می‌شمارد (بدون فیلتر deleted_at) — ممکن است ایمیل حساب حذف‌شده را قفل کند (**Verified در کد `email_exists`**).  
5. **full_name_exists** مشابه بدون فیلتر soft-delete.  
6. اختلاف FK CASCADE vs SET NULL بین dump و migration.

---

## مالکیت داده

همه موجودیت‌های دامنه به `user_id` وابسته‌اند. isolation چندمستأجری با فیلتر user_id در سرویس/مدل تأمین می‌شود، نه با schema جدا.
