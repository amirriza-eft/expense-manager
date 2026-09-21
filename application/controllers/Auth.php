<?php
class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->library('form_validation');
        $this->load->helpers('helper');
    }
    public function login() {
        $data['page_title'] = 'ورود به حساب';
        $this->load->view('login', $data);
    }

    public function profile() {
        $data['page_title'] = 'مشخصات کاربر';
        $this->load->view('profile', $data);
    }

    public function register()
    {
        if ($this->input->method() === 'post') {

            $this->form_validation->set_rules(
                'full_name',
                'Name',
                'required|min_length[3]|max_length[100]'
            );

            $this->form_validation->set_rules(
                'email',
                'Email',
                'required|valid_email|max_length[100]'
            );

            $this->form_validation->set_rules(
                'password',
                'Password',
                'required|min_length[8]|max_length[100]'
            );

            $this->form_validation->set_rules(
                'password_confirm',
                'Confirm Password',
                'required|min_length[8]|max_length[100]|matches[password]'
            );

            if ($this->form_validation->run() === FALSE) {

                $data['page_title'] = 'ثبت‌نام کاربر';
                $this->load->view('signup', $data);

                return;
            }

            $email = $this->input->post('email', true);
            $user = $this->User_model->find_by_email($email);

            if ($user) {
                echo "این ایمیل قبلا ثبت شده است";
                return;
            }

            $data = [
                'full_name' =>
                    $this->input->post('full_name', true),

                'email' =>
                    $email,

                'password' =>
                    password_hash(
                        $this->input->post('password'),
                        PASSWORD_DEFAULT
                    ),

                'created_at' =>
                    date('Y-m-d H:i:s')
            ];



            if ($this->User_model->create($data)) {

                redirect('/login');

            }
        }

        $data['page_title'] = 'ثبت‌نام کاربر';
        $this->load->view('signup', $data);
    }
}