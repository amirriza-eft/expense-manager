<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('components/navbar'); ?>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <h3 class="fw-bold text-white mb-4">
                    پروفایل کاربری
                </h3>

                <?php $this->load->view('profile/info_card'); ?>
            </div>
        </div>
    </div>

<?php $this->load->view('components/footer'); ?>