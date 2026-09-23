<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>


<div class="glass-panel p-4">
    <h5 class="fw-bold text-white mb-4 border-bottom pb-2"
        style="border-color:var(--border-subtle)!important;">
        تغییر کلمه عبور
    </h5>

    <div id="passwordMessage"
         class="alert d-none py-2 px-3 small">
    </div>

    <form id="passwordForm">
        <div class="mb-3">
            <label class="form-label text-light small">
                رمز عبور فعلی
            </label>
            <input
                    type="password"
                    class="form-control"
                    name="current_password"
                    required>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label text-light small">
                    رمز عبور جدید
                </label>

                <input
                        type="password"
                        class="form-control"
                        name="new_password"
                        required>
            </div>

            <div class="col-md-6">

                <label class="form-label text-light small">
                    تکرار رمز عبور جدید
                </label>

                <input
                        type="password"
                        class="form-control"
                        name="new_password_confirm"
                        required>
            </div>

        </div>

        <button class="btn btn-orange-outline">
            تغییر رمز عبور
        </button>

    </form>


</div>


<script>
    document.getElementById('passwordForm')
        .addEventListener('submit', function (e) {

            e.preventDefault();

            let box = document.getElementById('passwordMessage');

            fetch(
                "<?= site_url('api/profile/password') ?>",
                {
                    method: "POST",
                    body: new FormData(this)
                }
            )
                .then(res => res.json())
                .then(data => {

                    box.classList.remove('d-none');

                    if (data.status) {
                        box.className =
                            "alert alert-success py-2 px-3 small";
                    } else {
                        box.className =
                            "alert alert-danger py-2 px-3 small";
                    }

                    box.innerHTML = data.message;

                });
        });
</script>


<div class="glass-panel p-4 mt-4">

    <h5 class="fw-bold text-white mb-3">
        حذف حساب کاربری
    </h5>

    <p class="text-muted small mb-3">
        با حذف حساب، اطلاعات شما ابتدا غیرفعال می‌شود و پس از ۳۰ روز به‌صورت کامل حذف خواهد شد.
    </p>

    <button
            class="btn btn-outline-danger btn-sm"
            data-bs-toggle="modal"
            data-bs-target="#deleteAccountModal">
        <i class="bi bi-person-x"></i>
        حذف حساب کاربری
    </button>

</div>


<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content glass-panel text-white"
             style="background:#1e1e24;border:1px solid var(--border-subtle);">

            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">
                    حذف حساب کاربری
                </h5>
                <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body text-center">

                <i class="bi bi-exclamation-triangle text-danger fs-1 mb-3"></i>

                <p class="text-muted small">
                    با حذف حساب، حساب شما غیرفعال می‌شود.
                    <br>
                    تا ۳۰ روز امکان بازیابی حساب وجود دارد.
                    <br>
                    پس از آن اطلاعات به‌صورت کامل حذف خواهد شد.
                </p>


                <div id="deleteAccountMessage"
                     class="alert d-none py-2 small">
                </div>


                <input
                        type="password"
                        id="deleteAccountPassword"
                        class="form-control mb-3"
                        placeholder="رمز عبور">


                <div class="d-flex justify-content-center gap-2">

                    <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        انصراف
                    </button>


                    <button
                            type="button"
                            class="btn btn-danger"
                            onclick="deleteAccount()">
                        حذف حساب
                    </button>

                </div>

            </div>
        </div>
    </div>
</div>


<script>
    function deleteAccount() {

        let password =
            document.getElementById('deleteAccountPassword').value;


        let message =
            document.getElementById('deleteAccountMessage');


        if (!password) {
            message.classList.remove('d-none');
            message.className =
                "alert alert-danger py-2 small";

            message.innerHTML =
                "لطفاً رمز عبور خود را وارد کنید";

            return;
        }


        fetch(
            "<?= site_url('api/profile/delete') ?>",
            {
                method: "POST",

                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },

                body: new URLSearchParams({
                    password: password
                })
            }
        )


            .then(res => res.json())


            .then(data => {


                message.classList.remove('d-none');


                if (data.status) {

                    message.className =
                        "alert alert-success py-2 small";

                    message.innerHTML =
                        data.message;


                    setTimeout(() => {

                        window.location.href =
                            "<?= site_url('login') ?>";

                    }, 1500);

                } else {

                    message.className =
                        "alert alert-danger py-2 small";

                    message.innerHTML =
                        data.message;

                }

            });


    }
</script>