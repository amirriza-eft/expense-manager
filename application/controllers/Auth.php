<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

    public function login()
    {
        $data['page_title'] = 'ورود به حساب';

        $this->load->view('login', $data);
    }


    public function register()
    {
        $data['page_title'] = 'ثبت‌نام';

        $this->load->view('signup', $data);
    }


    public function profile()
    {
        $data['page_title'] = 'پروفایل';

        $this->load->view('profile', $data);
    }

}