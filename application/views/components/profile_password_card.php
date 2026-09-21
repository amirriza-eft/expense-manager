<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>


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

        <button class="btn btn-orange-outline px-4">
            تغییر رمز عبور
        </button>

    </form>


</div>



<script>
    document.getElementById('passwordForm')
        .addEventListener('submit',function(e){

            e.preventDefault();

            let box=document.getElementById('passwordMessage');

            fetch(
                "<?= site_url('api/profile/password') ?>",
                {
                    method:"POST",
                    body:new FormData(this)
                }
            )
                .then(res=>res.json())
                .then(data=>{

                    box.classList.remove('d-none');

                    if(data.status)
                    {
                        box.className=
                            "alert alert-success py-2 px-3 small";
                    }
                    else
                    {
                        box.className=
                            "alert alert-danger py-2 px-3 small";
                    }

                    box.innerHTML=data.message;

                });
        });
</script>