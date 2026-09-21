<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="glass-panel p-4 p-sm-5 shadow-lg border" style="border-color:var(--border-subtle)!important;">

    <div class="text-center mb-4">
        <a href="<?= site_url('home') ?>" class="d-inline-block mb-3">
            <i class="bi bi-wallet2 brand-icon" style="font-size:2.75rem;"></i>
        </a>

        <h3 class="fw-bold text-white mb-2">
            ثبت‌نام در حساب‌یار
        </h3>

        <p class="text-muted small">
            مدیریت هوشمند درآمدها و هزینه‌های خود را آغاز کنید
        </p>
    </div>


    <div id="registerError"
         class="alert alert-danger py-2 px-3 small border-0 d-none"
         style="background:rgba(220,53,69,.15);color:#ff6b6b;">
    </div>


    <div id="registerSuccess"
         class="alert alert-success py-2 px-3 small border-0 d-none"
         style="background:rgba(16,185,129,.15);color:#10b981;">
    </div>


    <form id="registerForm">


        <div class="mb-3">
            <label class="form-label text-light small fw-medium">
                نام و نام خانوادگی
            </label>

            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-person"></i>
                </span>

                <input type="text"
                       class="form-control"
                       name="full_name"
                       placeholder="مثل: بابک زنجانی"
                       required>
            </div>
        </div>



        <div class="mb-3">
            <label class="form-label text-light small fw-medium">
                ایمیل
            </label>

            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-envelope"></i>
                </span>

                <input type="email"
                       class="form-control"
                       name="email"
                       placeholder="name@example.com"
                       required>
            </div>
        </div>



        <div class="mb-3">
            <label class="form-label text-light small fw-medium">
                رمز عبور
            </label>

            <div class="input-group">

                <span class="input-group-text">
                    <i class="bi bi-shield-lock"></i>
                </span>

                <input type="password"
                       class="form-control"
                       id="password"
                       name="password"
                       placeholder="حداقل ۸ کاراکتر"
                       required>

                <button type="button"
                        class="input-group-text toggle-password"
                        data-target="password">

                    <i class="bi bi-eye"></i>

                </button>

            </div>
        </div>



        <div class="mb-4">
            <label class="form-label text-light small fw-medium">
                تکرار رمز عبور
            </label>

            <div class="input-group">

                <span class="input-group-text">
                    <i class="bi bi-shield-check"></i>
                </span>

                <input type="password"
                       class="form-control"
                       id="password_confirm"
                       name="password_confirm"
                       placeholder="تکرار رمز عبور"
                       required>

            </div>
        </div>



        <button type="submit"
                class="btn btn-orange-glow w-100"
                id="regSubmitBtn">
            ایجاد حساب کاربری
        </button>


    </form>



    <div class="text-center mt-4 pt-3 border-top"
         style="border-color:var(--border-subtle)!important;">

        <span class="text-muted small">
            قبلاً حساب ساخته‌اید؟
        </span>

        <a href="<?= site_url('login') ?>"
           class="text-decoration-none small fw-bold"
           style="color:var(--accent-orange);">

            وارد شوید

        </a>

    </div>


</div>


<script>
    document.addEventListener('DOMContentLoaded',()=>{

        document.querySelectorAll('.toggle-password').forEach(btn=>{
            btn.addEventListener('click',function(){

                const input=document.getElementById(this.dataset.target);
                const icon=this.querySelector('i');

                if(input.type==="password"){
                    input.type="text";
                    icon.classList.replace('bi-eye','bi-eye-slash');
                }else{
                    input.type="password";
                    icon.classList.replace('bi-eye-slash','bi-eye');
                }

            });
        });



        document.getElementById('registerForm')
            .addEventListener('submit',function(e){

                e.preventDefault();

                const errorBox=document.getElementById('registerError');
                const successBox=document.getElementById('registerSuccess');
                const btn=document.getElementById('regSubmitBtn');

                const password=document.getElementById('password').value;
                const confirmPassword=document.getElementById('password_confirm').value;


                errorBox.classList.add('d-none');
                successBox.classList.add('d-none');


                if(password!==confirmPassword){

                    errorBox.innerHTML="رمز عبور و تکرار آن یکسان نیست";
                    errorBox.classList.remove('d-none');

                    return;
                }



                btn.disabled=true;
                btn.innerHTML="در حال ثبت نام...";


                fetch(
                    "<?= site_url('api/auth/register') ?>",
                    {
                        method:"POST",
                        body:new FormData(this)
                    }
                )

                    .then(response=>response.json())

                    .then(data=>{

                        if(data.status){

                            successBox.innerHTML=data.message;
                            successBox.classList.remove('d-none');


                            setTimeout(()=>{

                                window.location.href=
                                    "<?= site_url('login') ?>";

                            },1000);


                        }else{

                            errorBox.innerHTML=data.message;
                            errorBox.classList.remove('d-none');

                        }

                    })

                    .catch(()=>{

                        errorBox.innerHTML="خطا در ارتباط با سرور";
                        errorBox.classList.remove('d-none');

                    })

                    .finally(()=>{

                        btn.disabled=false;
                        btn.innerHTML="ایجاد حساب کاربری";

                    });

            });

    });
</script>