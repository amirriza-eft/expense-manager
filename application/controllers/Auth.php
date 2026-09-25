<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('User_model');
    }

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
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            return;
        }

        $this->syncSessionProfile();

        $data['page_title'] = 'پروفایل';
        $this->load->view('profile', $data);
    }

    protected function syncSessionProfile()
    {
        if ($this->session->userdata('avatar_synced')) {
            return;
        }

        $user = $this->User_model->find($this->session->userdata('user_id'));
        if (!$user) {
            return;
        }

        $this->session->set_userdata([
            'full_name' => $user->full_name,
            'user_name' => $user->full_name,
            'user_avatar' => $user->avatar ?? null,
            'email' => $user->email,
            'avatar_synced' => true,
        ]);
    }
}
