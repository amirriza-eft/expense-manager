<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . 'controllers/api/Base_API_Controller.php');

class Auth_API_Controller extends Base_API_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->library('session');

        require_once APPPATH . 'policies/Registration_policy.php';
        $this->registration_policy = new Registration_policy();
    }

    public function register()
    {
        $user_id = $this->session->userdata('user_id');

        if (!$this->registration_policy->can_register($user_id)) {
            return $this->json([
                'status' => false,
                'message' => 'شما قبلا وارد شده‌اید'
            ], 403);
        }

        $this->form_validation->set_rules(
            'full_name',
            'نام و نام خانوادگی',
            'required|min_length[3]|max_length[100]',
            [
                'required' => 'وارد کردن {field} الزامی است.',
                'min_length' => '{field} باید حداقل ۳ کاراکتر باشد.',
                'max_length' => '{field} نباید بیشتر از ۱۰۰ کاراکتر باشد.'
            ]
        );

        $this->form_validation->set_rules(
            'email',
            'ایمیل',
            'required|valid_email|max_length[100]',
            [
                'required' => 'وارد کردن {field} الزامی است.',
                'valid_email' => 'فرمت {field} صحیح نیست.',
                'max_length' => '{field} نباید بیشتر از ۱۰۰ کاراکتر باشد.'
            ]
        );

        $this->form_validation->set_rules(
            'password',
            'رمز عبور',
            'required|min_length[8]|max_length[100]',
            [
                'required' => 'وارد کردن {field} الزامی است.',
                'min_length' => '{field} باید حداقل ۸ کاراکتر باشد.',
                'max_length' => '{field} نباید بیشتر از ۱۰۰ کاراکتر باشد.'
            ]
        );

        $this->form_validation->set_rules(
            'password_confirm',
            'تکرار رمز عبور',
            'required|matches[password]',
            [
                'required' => 'وارد کردن {field} الزامی است.',
                'matches' => '{field} با رمز عبور یکسان نیست.'
            ]
        );

        if ($this->form_validation->run() === FALSE) {
            return $this->json([
                'status' => false,
                'message' => validation_errors()
            ], 400);
        }

        $email = $this->input->post('email', true);

        $exists = $this->User_model->find_any_by_email($email);

        if ($exists) {
            return $this->json([
                'status' => false,
                'message' => 'این ایمیل قبلا ثبت شده است'
            ]);
        }

        $data = [
            'full_name' => $this->input->post('full_name', true),
            'email' => $email,
            'password' => password_hash(
                $this->input->post('password'),
                PASSWORD_DEFAULT
            ),
            'created_at' => date('Y-m-d H:i:s')
        ];


        $user_id = $this->User_model->create($data);

        if ($user_id) {
            $this->User_model->create_budget($user_id);

            return $this->json([
                'status' => true,
                'message' => 'ثبت نام موفق'
            ]);
        }

        return $this->json([
            'status' => false,
            'message' => 'خطا در ایجاد حساب'
        ], 500);
    }

    public function login()
    {
        $data = json_decode($this->input->raw_input_stream, true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        $user = $this->User_model->find_by_email($email);

        if ($user) {
            if (!password_verify($password, $user->password)) {
                return $this->json([
                    'status' => false,
                    'message' => 'ایمیل یا رمز عبور اشتباه است'
                ]);
            }

            $this->session->set_userdata([
                'user_id' => $user->id,
                'full_name' => $user->full_name,
                'user_name' => $user->full_name,
                'user_avatar' => $user->avatar ?? null,
                'email' => $user->email,
                'logged_in' => true
            ]);

            return $this->json([
                'status' => true,
                'message' => 'ورود موفق'
            ]);
        }

        $deleted = $this->User_model->find_deleted_by_email($email);

        if ($deleted && password_verify($password, $deleted->password)) {
            return $this->json([
                'status' => false,
                'code' => 'account_deleted',
                'message' => 'این حساب حذف شده است. برای بازیابی حساب کلیک کنید.'
            ]);
        }

        return $this->json([
            'status' => false,
            'message' => 'ایمیل یا رمز عبور اشتباه است'
        ]);
    }

    public function restore()
    {
        $email = $this->input->post('email', true);
        $password = $this->input->post('password');

        if (!$email || !$password) {
            return $this->json([
                'status' => false,
                'message' => 'ایمیل و رمز عبور الزامی است'
            ], 400);
        }

        $user = $this->User_model->find_deleted_by_email($email);

        if (!$user || !password_verify($password, $user->password)) {
            return $this->json([
                'status' => false,
                'message' => 'ایمیل یا رمز عبور اشتباه است'
            ]);
        }

        if ($this->User_model->restore($user->id)) {
            return $this->json([
                'status' => true,
                'message' => 'حساب شما بازیابی شد. اکنون می‌توانید وارد شوید'
            ]);
        }

        return $this->json([
            'status' => false,
            'message' => 'خطا در بازیابی حساب'
        ], 500);
    }

    public function logout()
    {
        $this->session->sess_destroy();

        return $this->json([
            'status' => true,
            'message' => 'خروج موفق'
        ]);
    }

    public function delete_account()
    {
        $user_id = $this->session->userdata("user_id");

        if (!$user_id) {
            return $this->json([
                'status' => false,
                'message' => 'ابتدا وارد شوید'
            ], 401);
        }

        $password = $this->input->post("password", true);

        $user = $this->User_model->find($user_id);

        if (!$user || !password_verify($password, $user->password)) {
            return $this->json([
                'status' => false,
                'message' => 'رمز عبور اشتباه است'
            ]);
        }

        if ($this->User_model->soft_delete($user_id)) {
            $this->session->sess_destroy();

            return $this->json([
                'status' => true,
                'message' => 'حساب شما حذف شد. تا ۳۰ روز امکان بازیابی وجود دارد'
            ]);
        }

        return $this->json([
            'status' => false,
            'message' => 'خطا در حذف حساب'
        ]);
    }

}