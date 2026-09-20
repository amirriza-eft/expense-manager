<!-- Hero Section -->
<section class="py-5 my-md-5 position-relative overflow-hidden">
    <!-- Ambient orange glow effect in background -->
    <div style="position: absolute; top: -100px; left: 50%; transform: translateX(-50%); width: 500px; height: 350px; background: radial-gradient(circle, rgba(255,107,0,0.18) 0%, rgba(18,18,18,0) 70%); pointer-events: none; z-index: 0;"></div>

    <div class="container position-relative" style="z-index: 1;">
        <div class="row align-items-center justify-content-center text-center">
            <div class="col-lg-10 col-xl-8">
                <span class="badge py-2 px-3 rounded-pill mb-3" style="background: rgba(255, 107, 0, 0.12); color: var(--accent-orange); border: 1px solid var(--border-orange);">
                    <i class="bi bi-lightning-charge-fill me-1"></i> نسل جدید زیرساخت‌های ابری و امن
                </span>

                <h1 class="display-4 fw-black mb-4 text-white lh-base">
                    طراحی سریع و قدرتمند با
                    <span style="background: linear-gradient(135deg, #ff6b00 0%, #ffaa00 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">تم اختصاصی نئون نارنجی</span>
                </h1>

                <p class="lead text-muted mb-5 mx-auto px-md-4" style="font-size: 1.15rem; font-weight: 300; line-height: 1.9;">
                    یک بستر ماژولار، با ساختار استاندارد و واکنش‌گرا هماهنگ با نیازمندی‌های پروژه‌های فارسی‌زبان تحت وب بر پایه آخرین امکانات Bootstrap 5.
                </p>

                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="register.html" class="btn btn-orange-glow btn-lg px-4 py-3 fw-bold">
                        <i class="bi bi-rocket-takeoff ms-2"></i> شروع رایگان در چند ثانیه
                    </a>
                    <a href="#services" class="btn btn-orange-outline btn-lg px-4 py-3 fw-bold">
                        <i class="bi bi-play-circle ms-2"></i> مشاهده دمو و ویژگی‌ها
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Feature/Services Cards Section -->
<section class="py-5 mb-5" id="services">
    <div class="container">
        <div class="text-center mb-5">
            <h6 class="text-uppercase fw-bold" style="color: var(--accent-orange); letter-spacing: 1px;">خدمات و قابلیت‌ها</h6>
            <h2 class="fw-bold text-white mt-2">چرا پلتفرم ما را انتخاب می‌کنند؟</h2>
            <p class="text-muted">برنامه‌ریزی، توسعه و پیاده‌سازی بر اساس برترین استانداردهای روز دنیا</p>
        </div>

        <div class="row g-4">
            <!-- Card 1 -->
            <div class="col-md-4">
                <div class="card h-100 glass-panel p-4 feature-card">
                    <div class="card-icon-wrapper mb-4">
                        <i class="bi bi-shield-check fs-2" style="color: var(--accent-orange);"></i>
                    </div>
                    <h5 class="fw-bold text-white mb-3">امنیت پیشرفته و اختصاصی</h5>
                    <p class="text-muted small lh-lg mb-0">
                        بهره‌گیری از پروتکل‌های احراز هویت قوی، محافظت در برابر حملات متداول و رعایت استانداردهای بالای امنیتی در ساختار نرم‌افزار.
                    </p>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="col-md-4">
                <div class="card h-100 glass-panel p-4 feature-card">
                    <div class="card-icon-wrapper mb-4">
                        <i class="bi bi-speedometer2 fs-2" style="color: var(--accent-orange);"></i>
                    </div>
                    <h5 class="fw-bold text-white mb-3">سرعت لودینگ برق‌آسا</h5>
                    <p class="text-muted small lh-lg mb-0">
                        کدنویسی سبک و مینیمال بدون وابستگی به فریم‌ورک‌های سنگین جانبی؛ سریع‌ترین لود بر بستر اینترنت با تایپوگرافی فارسی وزیرمتن.
                    </p>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="col-md-4">
                <div class="card h-100 glass-panel p-4 feature-card">
                    <div class="card-icon-wrapper mb-4">
                        <i class="bi bi-palette fs-2" style="color: var(--accent-orange);"></i>
                    </div>
                    <h5 class="fw-bold text-white mb-3">طراحی واکنش‌گرا و ارگونومیک</h5>
                    <p class="text-muted small lh-lg mb-0">
                        نمایش بدون عیب در تمام موبایل‌ها، تبلت‌ها و دسکتاپ‌ها به همراه پنل شیشه‌ای ملایم برای کاهش خستگی چشم کاربران.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .card-icon-wrapper {
        width: 64px;
        height: 64px;
        border-radius: 14px;
        background: rgba(255, 107, 0, 0.08);
        border: 1px solid var(--border-orange);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 15px rgba(255, 107, 0, 0.15);
    }

    .feature-card {
        transition: all 0.35s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .feature-card:hover {
        transform: translateY(-8px);
        border-color: var(--accent-orange);
        box-shadow: 0 12px 30px rgba(255, 107, 0, 0.18);
    }
</style>
