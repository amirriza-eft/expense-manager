# ۱۱. نقشه‌های معماری

همه دیاگرام‌ها بر اساس پیاده‌سازی تأییدشده هستند.

---

## A. معماری سیستم

```mermaid
flowchart LR
    subgraph Client
      Browser[Browser RTL UI]
      Vue[Vue 3 Islands]
      Axios[Axios]
    end

    subgraph WebRoot[public/]
      Index[index.php]
      Assets[CSS/JS/CDN refs]
      UploadsPub[uploads symlink]
    end

    subgraph App[application/]
      Routes[routes.php]
      PageC[Page Controllers]
      ApiC[API Controllers]
      Svc[Services]
      Pol[Policies]
      Val[Validators]
      Models[Models]
    end

    DB[(MySQL)]
    Sess[File sessions]
    Disk[uploads/avatars]

    Browser --> Vue --> Axios --> Index --> Routes
    Routes --> PageC
    Routes --> ApiC
    ApiC --> Val
    ApiC --> Svc
    Svc --> Pol
    Svc --> Models
    PageC --> Sess
    ApiC --> Sess
    Models --> DB
    Svc --> Disk
    UploadsPub --> Disk
```

**وجود ندارد در سیستم:** Redis/cache اختصاصی، queue worker، payment gateway، mailer.

---

## B. چرخه درخواست HTTP

```mermaid
flowchart TD
    A[HTTP Request] --> B[public/index.php]
    B --> C[CI Bootstrap + autoload DB/session/form_validation]
    C --> D[Router: routes.php]
    D --> E{Page یا API؟}
    E -->|Home| F[Base_Controller: restore remember + authCheck]
    E -->|Auth pages| G[Auth controller بدون Base auth کامل]
    E -->|API| H[Base_API_Controller]
    F --> I[Load PHP View + Vue]
    G --> I
    H --> J{auth_user در متد؟}
    J -->|بله و fail| K[JSON 401 exit]
    J -->|ok / public auth| L[Validator? Service Model]
    L --> M[json encode exit]
```

---

## C. چرخه احراز هویت

```mermaid
sequenceDiagram
    participant U as User
    participant Login as login_card
    participant API as Auth_API
    participant S as Auth_service
    participant DB as DB
    participant Home as Base_Controller

    U->>Login: submit email/password
    Login->>API: POST /api/auth/login
    API->>S: login(...)
    S->>DB: find_by_email + password_verify
    S->>S: set_userdata session
    opt remember_me
        S->>DB: insert remember_tokens
        S->>U: Set-Cookie remember_me
    end
    Login->>U: redirect /home
    U->>Home: GET /
    Home->>S: restore_remembered_session if needed
    Home->>U: dashboard HTML
```

---

## D. جریان اصلی کسب‌وکار: تراکنش و بودجه

```mermaid
flowchart TD
    A[Vue saveTransaction] --> B[POST create/update]
    B --> C[Transaction_validator]
    C --> D[Transaction_service]
    D --> E[validate_category active+type match]
    E --> F[Transaction_model]
    F --> G[DB transaction]
    G --> H[change_budget +/-]
    G --> I[insert/update row]
    H --> J[budgets.amount]
    I --> K[JSON success]
    K --> L[reload list + dashboard + chart]
```

---

## E. ER دیتابیس

← رجوع به `06-database-and-data-lifecycle.md`

---

## F. وابستگی ماژول‌ها

```mermaid
flowchart TB
    AuthMod[Auth module] --> Users[(users)]
    AuthMod --> Tokens[(remember_tokens)]
    AuthMod --> Budgets[(budgets)]

    ProfileMod[Profile] --> Users
    ProfileMod --> Disk[avatars files]

    CatMod[Categories] --> Cats[(categories)]
    CatMod --> Tx[(transactions)]
    CatMod --> Budgets

    TxMod[Transactions] --> Tx
    TxMod --> Cats
    TxMod --> Budgets

    Dash[Dashboard/Chart] --> Budgets
    Dash --> Tx
```

---

## G. یکپارچگی فرانت/بک

```mermaid
flowchart LR
    subgraph Pages
      LoginP[login]
      HomeP[home]
      ProfP[profile]
    end

    subgraph APIs
      AuthAPI[api/auth/*]
      ProfAPI[api/profile/*]
      TxAPI[api/transaction*]
      CatAPI[api/categories*]
      DashAPI[api/dashboard + chart]
    end

    LoginP --> AuthAPI
    HomeP --> DashAPI
    HomeP --> TxAPI
    HomeP --> CatAPI
    ProfP --> ProfAPI
    ProfP --> AuthAPI
```
