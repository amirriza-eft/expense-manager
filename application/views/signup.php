<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('components/navbar'); ?>

<section class="py-5 min-vh-100 d-flex align-items-center position-relative">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 450px; height: 450px; background: radial-gradient(circle, rgba(255,107,0,0.15) 0%, rgba(18,18,18,0) 70%); pointer-events: none;"></div>

    <div class="container position-relative">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-6 col-xl-5">
                <?php $this->load->view('auth/register_card'); ?>
            </div>
        </div>
    </div>
</section>

<?php $this->load->view('components/footer'); ?>
