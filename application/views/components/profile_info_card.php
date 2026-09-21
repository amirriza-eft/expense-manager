<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="glass-panel p-4 mb-4">
    <h5 class="fw-bold text-white mb-4 border-bottom pb-2"
        style="border-color:var(--border-subtle)!important;">
        اطلاعات کاربری و تصویر پروفایل
    </h5>

    <div id="profileMessage"
         class="alert d-none py-2 px-3 small">
    </div>

    <form id="profileForm" enctype="multipart/form-data">
        <div class="d-flex flex-column flex-sm-row align-items-center gap-4 mb-4">
            <div>
                <img
                        src="https://placehold.co/100x100/1e1e24/ff6b00?text=%20"
                        id="avatarPreview"
                        class="rounded-circle border"
                        alt="Avatar"
                        style="
                width:100px;
                height:100px;
                object-fit:cover;
                border-color:var(--accent-orange)!important;
                background-color:var(--bg-surface);
                ">
            </div>

            <div class="flex-grow-1">

                <label class="form-label small text-muted">
                    تصویر پروفایل
                </label>

                <input
                        type="file"
                        name="avatar"
                        id="avatarInput"
                        class="form-control"
                        accept="image/png,image/jpeg,image/webp">


                <small class="text-muted">
                    JPG PNG WEBP - حداکثر ۲ مگابایت
                </small>
            </div>
        </div>

        <div class="mb-3">

            <label class="form-label text-light small">
                نام و نام خانوادگی
            </label>

            <input
                    type="text"
                    class="form-control"
                    name="full_name"
                    id="full_name"
                    required>
        </div>

        <div class="mb-4">
            <label class="form-label text-light small">
                ایمیل
            </label>

            <input
                    type="email"
                    class="form-control"
                    name="email"
                    id="email"
                    required>

        </div>

        <button class="btn btn-orange-glow">
            ذخیره اطلاعات
        </button>

    </form>
</div>



<script>

    document.addEventListener('DOMContentLoaded',()=>{

        loadProfile();

        function loadProfile()
        {
            fetch("<?= site_url('api/profile') ?>")
                .then(res=>res.json())
                .then(data=>{

                    if(data.status)
                    {
                        document.getElementById('full_name').value =
                            data.user.full_name;

                        document.getElementById('email').value =
                            data.user.email;

                        if(data.user.avatar)
                        {
                            document.getElementById('avatarPreview').src =
                                "<?= base_url('uploads/avatars/') ?>"+data.user.avatar;
                        }
                    }
                });
        }

        document.getElementById('avatarInput')
            .addEventListener('change',function(e){
                let file=e.target.files[0];
                if(file)
                {
                    let reader=new FileReader();
                    reader.onload=function(event){

                        document.getElementById('avatarPreview').src =
                            event.target.result;

                    }
                    reader.readAsDataURL(file);
                }
            });

        document.getElementById('profileForm')
            .addEventListener('submit',function(e){

                e.preventDefault();

                let box=document.getElementById('profileMessage');

                fetch(
                    "<?= site_url('api/profile/update') ?>",
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
    });
</script>