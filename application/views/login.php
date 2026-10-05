<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('components/navbar'); ?>

    <section class="py-5 min-vh-100 d-flex align-items-center position-relative">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 400px; height: 400px; background: radial-gradient(circle, rgba(255,107,0,0.15) 0%, rgba(18,18,18,0) 70%); pointer-events: none;"></div>

        <div class="container position-relative">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-5 col-xl-4">
                    <?php $this->load->view('auth/login_card'); ?>
                </div>
            </div>
        </div>
    </section>

<?php $this->load->view('components/footer'); ?>