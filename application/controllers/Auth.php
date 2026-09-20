<?php
class Auth extends CI_Controller {
    public function login() {
        $data['page_title'] = 'ورود به حساب';
        $this->load->view('components/header', $data);
        $this->load->view('login', $data);
        $this->load->view('components/footer', $data);
    }

    public function signup() {
        $data['page_title'] = 'ثبت‌نام کاربر';
        $this->load->view('components/header', $data);
        $this->load->view('signup', $data);
        $this->load->view('components/footer', $data);
    }
}