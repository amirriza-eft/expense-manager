<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
</main>

<footer class="mt-auto border-top" style="background-color: #101014; border-color: var(--border-subtle) !important;">
    <div class="container py-4">
        <div class="row align-items-center justify-content-between g-3">
            <div class="col-md-6 text-center text-md-start">
                <span class="text-muted small">
                    Created by <strong class="text-light">amirreza eftekharzade</strong> &copy; <?= date('Y') ?> حساب‌یار
                </span>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <a href="https://github.com/amirreza-eftekharzade" target="_blank" rel="noopener noreferrer" class="text-muted text-decoration-none small hover-orange d-inline-flex align-items-center gap-1">
                    <i class="bi bi-github fs-5 text-light"></i>
                    <span>مشاهده سورس در گیت‌هاب</span>
                </a>
            </div>
        </div>
    </div>
</footer>

<style>
    .hover-orange:hover {
        color: var(--accent-orange) !important;
        transition: color 0.2s ease;
    }
</style>

<!-- Bootstrap 5 Bundle JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>